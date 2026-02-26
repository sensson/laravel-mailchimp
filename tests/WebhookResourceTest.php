<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Data\Webhook;
use Sensson\Mailchimp\Enums\WebhookEvent;
use Sensson\Mailchimp\Enums\WebhookSource;
use Sensson\Mailchimp\Facades\Mailchimp;
use Sensson\Mailchimp\Requests\Webhooks\CreateWebhook;
use Sensson\Mailchimp\Requests\Webhooks\DeleteWebhook;

it('creates a webhook', function (): void {
    $mock = new MockClient([
        CreateWebhook::class => MockResponse::make([
            'id' => 'wh123',
            'url' => 'https://example.com/webhook',
            'list_id' => 'list-123',
        ]),
    ]);

    Mailchimp::fake($mock);

    $webhook = Mailchimp::webhooks('list-123')->create('https://example.com/webhook', [WebhookEvent::Subscribe, WebhookEvent::Unsubscribe]);

    expect($webhook)
        ->toBeInstanceOf(Webhook::class)
        ->id->toBe('wh123')
        ->url->toBe('https://example.com/webhook');

    $mock->assertSent(function (CreateWebhook $request): bool {
        $body = $request->body()->all();

        return $body['url'] === 'https://example.com/webhook'
            && $body['events'] === ['subscribe' => true, 'unsubscribe' => true];
    });
});

it('creates a webhook with sources', function (): void {
    $mock = new MockClient([
        CreateWebhook::class => MockResponse::make([
            'id' => 'wh123',
            'url' => 'https://example.com/webhook',
        ]),
    ]);

    Mailchimp::fake($mock);

    Mailchimp::webhooks('list-123')->create(
        'https://example.com/webhook',
        [WebhookEvent::Subscribe],
        [WebhookSource::User, WebhookSource::Admin],
    );

    $mock->assertSent(function (CreateWebhook $request): bool {
        $body = $request->body()->all();

        return $body['events'] === ['subscribe' => true]
            && $body['sources'] === ['user' => true, 'admin' => true];
    });
});

it('creates a webhook without events', function (): void {
    $mock = new MockClient([
        CreateWebhook::class => MockResponse::make([
            'id' => 'wh123',
            'url' => 'https://example.com/webhook',
        ]),
    ]);

    Mailchimp::fake($mock);

    Mailchimp::webhooks('list-123')->create('https://example.com/webhook');

    $mock->assertSent(function (CreateWebhook $request): bool {
        return ! array_key_exists('events', $request->body()->all());
    });
});

it('deletes a webhook', function (): void {
    $mock = new MockClient([
        DeleteWebhook::class => MockResponse::make([], 204),
    ]);

    Mailchimp::fake($mock);

    Mailchimp::webhooks('list-123')->delete('wh123');

    $mock->assertSent(DeleteWebhook::class);
});
