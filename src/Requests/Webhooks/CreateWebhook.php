<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Webhooks;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Sensson\Mailchimp\Data\Webhook;
use Sensson\Mailchimp\Enums\WebhookEvent;
use Sensson\Mailchimp\Enums\WebhookSource;

final class CreateWebhook extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, WebhookEvent>  $events
     * @param  array<int, WebhookSource>  $sources
     */
    public function __construct(
        protected readonly string $list,
        protected readonly string $url,
        protected readonly array $events = [],
        protected readonly array $sources = [],
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}/webhooks";
    }

    /** @return array<string, string|array<string, bool>> */
    protected function defaultBody(): array
    {
        $body = ['url' => $this->url];

        if ($this->events !== []) {
            $body['events'] = collect($this->events)
                ->mapWithKeys(fn (WebhookEvent $event): array => [$event->value => true])
                ->all();
        }

        if ($this->sources !== []) {
            $body['sources'] = collect($this->sources)
                ->mapWithKeys(fn (WebhookSource $source): array => [$source->value => true])
                ->all();
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): Webhook
    {
        return Webhook::from($response->json());
    }
}
