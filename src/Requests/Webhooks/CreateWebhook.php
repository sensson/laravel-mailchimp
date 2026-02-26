<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Webhooks;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Sensson\Mailchimp\Data\Webhook;

final class CreateWebhook extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /** @param array<int, string> $events */
    public function __construct(
        protected readonly string $list,
        protected readonly string $url,
        protected readonly array $events = [],
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
                ->mapWithKeys(fn (string $event): array => [$event => true])
                ->all();
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): Webhook
    {
        return Webhook::from($response->json());
    }
}
