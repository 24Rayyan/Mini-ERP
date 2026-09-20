<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Menggunakan method getSetting() sesuai dengan yang ada di Model Setting kamu
            try {
                static $globalSetting = null;
                if ($globalSetting === null && class_exists(Setting::class)) {
                    $globalSetting = Setting::getSetting();
                }
                $view->with('globalSetting', $globalSetting);
            } catch (\Exception $e) {
                // Mencegah error saat jalankan migration sebelum tabel ada
                $view->with('globalSetting', null);
            }
        });
    }
}