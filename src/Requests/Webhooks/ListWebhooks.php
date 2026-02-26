<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Mailchimp\Data\Webhook;

final class ListWebhooks extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $list,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}/webhooks";
    }

    /** @return array<int, Webhook> */
    public function createDtoFromResponse(Response $response): array
    {
        /** @var array<int, array<string, mixed>> $webhooks */
        $webhooks = $response->json('webhooks') ?? [];

        return array_map(fn (array $webhook): Webhook => Webhook::from($webhook), $webhooks);
    }
}
