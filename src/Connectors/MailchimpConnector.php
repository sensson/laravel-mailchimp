<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Connectors;

use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\MemoryStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Sensson\Mailchimp\Enums\ServerPrefix;
use Sensson\Mailchimp\Exceptions\AccessTokenRevokedException;
use Sensson\Mailchimp\Resources\AudienceResource;
use Sensson\Mailchimp\Resources\MemberResource;
use Throwable;

class MailchimpConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;
    use HasRateLimits;

    public function __construct(
        protected ServerPrefix $serverPrefix,
        protected string $accessToken,
    ) {
        //
    }

    public function resolveBaseUrl(): string
    {
        return "https://{$this->serverPrefix->value}.api.mailchimp.com/3.0";
    }

    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->accessToken}",
        ];
    }

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(10)->everySeconds(1),
        ];
    }

    protected function resolveRateLimitStore(): MemoryStore
    {
        return new MemoryStore;
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        if ($response->status() === 401) {
            return new AccessTokenRevokedException($response, previous: $senderException);
        }

        return null;
    }

    public function audiences(): AudienceResource
    {
        return new AudienceResource($this);
    }

    public function members(string $listId): MemberResource
    {
        return new MemberResource($this, $listId);
    }
}
