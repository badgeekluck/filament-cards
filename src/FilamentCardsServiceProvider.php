<?php

namespace Kanuni\FilamentCards;

use Illuminate\Support\ServiceProvider;

class FilamentCardsServiceProvider extends ServiceProvider
{
    public static string $name = 'filament-cards';

    public function register(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', static::$name);
    }

    public function boot(): void
    {
        //
    }
}
