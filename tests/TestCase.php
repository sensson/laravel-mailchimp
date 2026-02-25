<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sensson\Mailchimp\MailchimpServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            MailchimpServiceProvider::class,
        ];
    }
}
