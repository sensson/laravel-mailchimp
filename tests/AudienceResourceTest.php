<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Data\Audience;
use Sensson\Mailchimp\Facades\Mailchimp;
use Sensson\Mailchimp\Requests\Audiences\GetAudience;
use Sensson\Mailchimp\Requests\Audiences\ListAudiences;

it('lists all audiences', function (): void {
    $mock = new MockClient([
        ListAudiences::class => MockResponse::make([
            'lists' => [
                ['id' => 'abc123', 'name' => 'Newsletter', 'member_count' => 150, 'web_id' => 1],
                ['id' => 'def456', 'name' => 'Updates', 'member_count' => 50, 'web_id' => 2],
            ],
        ]),
    ]);

    Mailchimp::fake($mock);

    $audiences = Mailchimp::audiences()->all();

    expect($audiences)
        ->toHaveCount(2)
        ->first()->toBeInstanceOf(Audience::class);

    expect($audiences->first())
        ->id->toBe('abc123')
        ->name->toBe('Newsletter')
        ->member_count->toBe(150);

    $mock->assertSent(ListAudiences::class);
});

it('gets a single audience', function (): void {
    $mock = new MockClient([
        GetAudience::class => MockResponse::make([
            'id' => 'abc123',
            'name' => 'Newsletter',
            'member_count' => 150,
            'web_id' => 1,
        ]),
    ]);

    Mailchimp::fake($mock);

    $audience = Mailchimp::audiences()->get('abc123');

    expect($audience)
        ->toBeInstanceOf(Audience::class)
        ->id->toBe('abc123')
        ->name->toBe('Newsletter')
        ->member_count->toBe(150);

    $mock->assertSent(GetAudience::class);
});
