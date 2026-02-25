<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Sensson\Mailchimp\Casts\MailchimpAuth;
use Sensson\Mailchimp\Enums\ServerPrefix;

it('casts a json string to an object with a server prefix enum', function (): void {
    $cast = new MailchimpAuth;

    $result = $cast->get(new User, 'auth', '{"accessToken":"token-123","serverPrefix":"us6"}', []);

    expect($result)
        ->accessToken->toBe('token-123')
        ->serverPrefix->toBe(ServerPrefix::Us6);
});

it('returns null for null values', function (): void {
    $cast = new MailchimpAuth;

    expect($cast->get(new User, 'auth', null, []))->toBeNull();
    expect($cast->set(new User, 'auth', null, []))->toBeNull();
});

it('serializes an object to json', function (): void {
    $cast = new MailchimpAuth;

    $result = $cast->set(new User, 'auth', (object) [
        'accessToken' => 'token-123',
        'serverPrefix' => ServerPrefix::Us6,
    ], []);

    expect($result)->toBe('{"accessToken":"token-123","serverPrefix":"us6"}');
});
