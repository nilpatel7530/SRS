<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

        // Dynamic System Branding
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $appName = \Modules\Admin\Models\Setting::get('app_name');
                $appLogo = \Modules\Admin\Models\Setting::get('app_logo');

                if ($appName) {
                    config(['adminlte.title' => $appName]);
                    config(['app.name' => $appName]);
                    config(['adminlte.logo' => '<b>' . $appName . '</b>']);
                }

                if ($appLogo) {
                    $logoUrl = (filter_var($appLogo, FILTER_VALIDATE_URL)) 
                        ? $appLogo 
                        : '/storage/' . $appLogo;
                    config(['adminlte.logo_img' => $logoUrl]);
                }
            }
        } catch (\Exception $e) {
            // Silence errors during migrations/artisan commands
        }
    }
}
