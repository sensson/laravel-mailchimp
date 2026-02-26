<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Resources;

use Illuminate\Support\Collection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Mailchimp\Data\Webhook;
use Sensson\Mailchimp\Enums\WebhookEvent;
use Sensson\Mailchimp\Enums\WebhookSource;
use Sensson\Mailchimp\Requests\Webhooks\CreateWebhook;
use Sensson\Mailchimp\Requests\Webhooks\DeleteWebhook;
use Sensson\Mailchimp\Requests\Webhooks\ListWebhooks;

final class WebhookResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        protected readonly string $list,
    ) {
        parent::__construct($connector);
    }

    /** @return Collection<int, Webhook> */
    public function all(): Collection
    {
        /** @var array<int, Webhook> $webhooks */
        $webhooks = $this->connector->send(new ListWebhooks($this->list))->dtoOrFail();

        return collect($webhooks);
    }

    /**
     * @param  array<int, WebhookEvent>  $events
     * @param  array<int, WebhookSource>  $sources
     */
    public function create(string $url, array $events = [], array $sources = []): Webhook
    {
        /** @var Webhook */
        return $this->connector->send(new CreateWebhook($this->list, $url, $events, $sources))->dtoOrFail();
    }

    public function delete(string $webhookId): void
    {
        $this->connector->send(new DeleteWebhook($this->list, $webhookId));
    }
}
