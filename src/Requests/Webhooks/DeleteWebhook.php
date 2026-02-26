<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteWebhook extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected readonly string $list,
        protected readonly string $webhookId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}/webhooks/{$this->webhookId}";
    }
}
