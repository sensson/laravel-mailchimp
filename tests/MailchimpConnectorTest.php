<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Connectors\MailchimpConnector;
use Sensson\Mailchimp\Enums\ServerPrefix;
use Sensson\Mailchimp\Exceptions\AccessTokenRevokedException;
use Sensson\Mailchimp\Requests\Audiences\ListAudiences;

it('resolves the base url with the server prefix', function (): void {
    $connector = new MailchimpConnector(ServerPrefix::Us6, 'test-token');

    expect($connector->resolveBaseUrl())->toBe('https://us6.api.mailchimp.com/3.0');
});

it('throws an exception on a 401 response', function (): void {
    $mock = new MockClient([
        ListAudiences::class => MockResponse::make(['detail' => 'API key is invalid'], 401),
    ]);

    $connector = new MailchimpConnector(ServerPrefix::Us6, 'bad-token');
    $connector->withMockClient($mock);

    $connector->audiences()->all();
})->throws(AccessTokenRevokedException::class);
