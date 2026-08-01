FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip \
        gd \
        mbstring \
        dom \
        pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# نسخ ملفات Composer أولًا للاستفادة من Docker Cache
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# نسخ بقية المشروع
COPY . .

RUN composer dump-autoload \
    --optimize \
    --no-dev \
    --no-interaction

# متغيرات Reverb المطلوبة أثناء بناء Vite
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME

ENV VITE_REVERB_APP_KEY=${VITE_REVERB_APP_KEY}
ENV VITE_REVERB_HOST=${VITE_REVERB_HOST}
ENV VITE_REVERB_PORT=${VITE_REVERB_PORT}
ENV VITE_REVERB_SCHEME=${VITE_REVERB_SCHEME}

# بناء ملفات CSS وJavaScript
RUN npm install
RUN npm run build

# إنشاء مجلدات Laravel وmPDF والتخزين الخاص
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    storage/app/private \
    storage/app/private/payment-receipts \
    storage/app/private/consultation-messages \
    storage/app/private/consultations/customer-files \
    storage/app/private/consultations/engineer-files \
    storage/app/mpdf-temp \
    bootstrap/cache

RUN php artisan storage:link || true

RUN chmod -R 775 storage bootstrap/cache

# إعدادات PHP ورفع الملفات وmPDF
RUN printf "upload_max_filesize=500M\n\
post_max_size=520M\n\
memory_limit=1024M\n\
max_execution_time=600\n\
max_input_time=600\n\
pcre.backtrack_limit=50000000\n\
pcre.recursion_limit=5000000\n" \
    > /usr/local/etc/php/conf.d/custom.ini

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
