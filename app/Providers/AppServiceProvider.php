<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Policies\ConversationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | إجبار HTTPS في بيئة الإنتاج
        |--------------------------------------------------------------------------
        */

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        /*
        |--------------------------------------------------------------------------
        | تسجيل صلاحيات المحادثات
        |--------------------------------------------------------------------------
        */

        Gate::policy(
            Conversation::class,
            ConversationPolicy::class
        );
    }
}
