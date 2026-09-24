<?php

namespace App\Providers;
use File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $menus = (object)['menus' => []];
        if (File::exists(base_path('resources/laravel-admin/menus.json'))) {
            $menus = json_decode(File::get(base_path('resources/laravel-admin/menus.json')));
        }

        // Merge section5 trader menus
        // (เมนู รับคำขอตรวจโรงงาน / ติดตามคำขอทดสอบผลิตภัณฑ์ ย้ายเข้าไปรวมอยู่ใน trader-menu-section5.json แล้ว
        //  ไม่โหลดจาก trader-menu-section5-inspection.json / trader-menu-section5-testing.json อีกต่อไป กันเมนูซ้ำ)
        $extraMenuFiles = [
            'resources/laravel-admin/trader-menu-section5.json',
        ];

        foreach ($extraMenuFiles as $menuFile) {
            if (File::exists(base_path($menuFile))) {
                $extra = json_decode(File::get(base_path($menuFile)));
                if ($extra && isset($extra->menus)) {
                    foreach ($extra->menus as $section) {
                        $menus->menus[] = $section;
                    }
                }
            }
        }

        view()->share('laravelAdminMenus', $menus);
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
