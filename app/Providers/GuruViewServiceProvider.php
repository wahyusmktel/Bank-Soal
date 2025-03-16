<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\DataUjian;

class GuruViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kirim data ke semua view guru
        View::composer('partials.navbarGuru', function ($view) {
            $dataUjianAktif = DataUjian::where('status', true)
                ->with('tahunPelajaran')
                ->latest()
                ->first();

            $view->with('dataUjianAktif', $dataUjianAktif);
        });
    }
}
