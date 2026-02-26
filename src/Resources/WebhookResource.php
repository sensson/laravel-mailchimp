<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Mailchimp\Data\Webhook;
use Sensson\Mailchimp\Requests\Webhooks\CreateWebhook;
use Sensson\Mailchimp\Requests\Webhooks\DeleteWebhook;

final class WebhookResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        protected readonly string $list,
    ) {
        parent::__construct($connector);
    }

    /** @param array<int, string> $events */
    public function create(string $url, array $events = []): Webhook
    {
        /** @var Webhook */
        return $this->connector->send(new CreateWebhook($this->list, $url, $events))->dtoOrFail();
    }

    public function delete(string $webhookId): void
    {
        $this->connector->send(new DeleteWebhook($this->list, $webhookId));
    }
}
