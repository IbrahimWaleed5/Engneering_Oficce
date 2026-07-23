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
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# نسخ ملفات Composer أولًا للاستفادة من Docker Cache
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# نسخ باقي المشروع
COPY . .

RUN composer dump-autoload \
    --optimize \
    --no-dev \
    --no-interaction

# بناء CSS وJavaScript
RUN npm install
RUN npm run build

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache

RUN php artisan storage:link || true

RUN chmod -R 775 storage bootstrap/cache

# إعدادات رفع الملفات
RUN printf "upload_max_filesize=500M\npost_max_size=520M\nmemory_limit=768M\nmax_execution_time=600\nmax_input_time=600\n" \
    > /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
