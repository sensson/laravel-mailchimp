<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Facades;

use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Sensson\Mailchimp\Connectors\AuthConnector;
use Sensson\Mailchimp\Connectors\MailchimpConnector;
use Sensson\Mailchimp\Enums\ServerPrefix;

/** @see MailchimpConnector */
class Mailchimp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MailchimpConnector::class;
    }

    public static function auth(): AuthConnector
    {
        return resolve(AuthConnector::class);
    }

    public static function make(ServerPrefix $serverPrefix, string $accessToken): MailchimpConnector
    {
        return new MailchimpConnector($serverPrefix, $accessToken);
    }

    public static function fake(MockClient $client): MailchimpConnector
    {
        $connector = new MailchimpConnector(ServerPrefix::Us1, 'fake-token');

        static::swap($connector->withMockClient($client));

        return $connector;
    }

    public static function authFake(MockClient $client): AuthConnector
    {
        $connector = (new AuthConnector)->withMockClient($client);

        app()->instance(AuthConnector::class, $connector);

        return $connector;
    }
}
