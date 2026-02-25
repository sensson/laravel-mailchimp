<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Facades\Mailchimp;
use Sensson\Mailchimp\Requests\Auth\ExchangeToken;
use Sensson\Mailchimp\Requests\Metadata\GetMetadata;

it('generates an authorization url', function (): void {
    config()->set('mailchimp.oauth.client_id', 'test-client-id');
    config()->set('mailchimp.oauth.redirect_uri', 'https://example.com/callback');

    $url = Mailchimp::auth()->getAuthorizationUrl();

    expect($url)
        ->toContain('https://login.mailchimp.com/oauth2/authorize')
        ->toContain('client_id=test-client-id')
        ->toContain('redirect_uri='.urlencode('https://example.com/callback'))
        ->toContain('response_type=code');
});

it('generates an authorization url with a custom redirect uri', function (): void {
    config()->set('mailchimp.oauth.client_id', 'test-client-id');

    $url = Mailchimp::auth()->getAuthorizationUrl('https://custom.com/callback');

    expect($url)->toContain('redirect_uri='.urlencode('https://custom.com/callback'));
});

it('exchanges a code for an access token', function (): void {
    config()->set('mailchimp.oauth.client_id', 'test-client-id');
    config()->set('mailchimp.oauth.client_secret', 'test-secret');
    config()->set('mailchimp.oauth.redirect_uri', 'https://example.com/callback');

    $mock = new MockClient([
        ExchangeToken::class => MockResponse::make(['access_token' => 'test-token-123']),
    ]);

    $connector = Mailchimp::authFake($mock);
    $token = $connector->exchangeToken('auth-code-456');

    expect($token)->toBe('test-token-123');

    $mock->assertSent(ExchangeToken::class);
});

it('fetches metadata to get the server prefix', function (): void {
    $mock = new MockClient([
        GetMetadata::class => MockResponse::make([
            'dc' => 'us6',
            'login_url' => 'https://login.mailchimp.com',
            'api_endpoint' => 'https://us6.api.mailchimp.com',
        ]),
    ]);

    $connector = Mailchimp::authFake($mock);
    $response = $connector->getMetadata('test-token');

    expect($response->json('dc'))->toBe('us6');
    expect($response->json('api_endpoint'))->toBe('https://us6.api.mailchimp.com');

    $mock->assertSent(GetMetadata::class);
});
