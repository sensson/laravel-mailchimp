<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Sensson\Mailchimp\Casts\MailchimpAuth;
use Sensson\Mailchimp\Data\MailchimpToken;
use Sensson\Mailchimp\Enums\ServerPrefix;

it('casts a json string to a mailchimp token', function (): void {
    $cast = new MailchimpAuth;

    $result = $cast->get(new User, 'auth', '{"accessToken":"token-123","serverPrefix":"us6"}', []);

    expect($result)
        ->toBeInstanceOf(MailchimpToken::class)
        ->accessToken->toBe('token-123')
        ->serverPrefix->toBe(ServerPrefix::Us6);
});

it('returns null for null values', function (): void {
    $cast = new MailchimpAuth;

    expect($cast->get(new User, 'auth', null, []))->toBeNull();
    expect($cast->set(new User, 'auth', null, []))->toBeNull();
});

it('serializes a mailchimp token to json', function (): void {
    $cast = new MailchimpAuth;

    $token = new MailchimpToken(
        accessToken: 'token-123',
        serverPrefix: ServerPrefix::Us6,
    );

    $result = $cast->set(new User, 'auth', $token, []);

    expect($result)->toBe('{"accessToken":"token-123","serverPrefix":"us6"}');
});
