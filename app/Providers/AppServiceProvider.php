<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Log database queries
        DB::listen(function (QueryExecuted $query) {
            Log::channel('daily')->info('Query Time: ' . $query->time . 'ms', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
            ]);
        });

        // Log authentication events
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function ($event) {
                Log::channel('daily')->info('User Logged In', [
                    'user_id' => $event->user->id,
                ]);
            }
        );

        // Log authentication failures
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Failed::class,
            function ($event) {
                Log::channel('daily')->warning('User Failed To Log In', [
                    'user' => [
                        'email' => $event->credentials['email'],
                    ],
                ]);
            }
        );

        // Log exceptions
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Foundation\Exceptions\Handler::class,
            function ($event) {
                Log::channel('daily')->error('Exception Occurred', [
                    'exception' => $event->getException()->getMessage(),
                ]);
            }
        );
    }

}
