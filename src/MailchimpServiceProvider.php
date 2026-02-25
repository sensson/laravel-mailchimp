<?php

declare(strict_types=1);

namespace Sensson\Mailchimp;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailchimpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mailchimp')
            ->hasConfigFile('mailchimp');

        config()->set('data.features.cast_and_transform_iterables', true);
    }
}
