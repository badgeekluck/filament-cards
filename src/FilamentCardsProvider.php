<?php

namespace Kanuni\FilamentCards;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCardsProvider extends PackageServiceProvider
{
    public static string $name = 'filament-cards';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted()
    {
    }
}
