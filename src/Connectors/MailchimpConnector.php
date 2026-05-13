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
use Sensson\Mailchimp\Exceptions\ForgottenEmailNotSubscribedException;
use Sensson\Mailchimp\Exceptions\MemberInComplianceStateException;
use Sensson\Mailchimp\Resources\AudienceResource;
use Sensson\Mailchimp\Resources\MemberResource;
use Sensson\Mailchimp\Resources\MergeFieldResource;
use Sensson\Mailchimp\Resources\WebhookResource;
use Throwable;

final class MailchimpConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;
    use HasRateLimits;

    public function __construct(
        protected readonly ServerPrefix $serverPrefix,
        protected readonly string $accessToken,
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
        if ($this->getMockClient() !== null) {
            return [];
        }

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

        if ($response->status() === 400) {
            return match ($response->json('title')) {
                'Member In Compliance State' => new MemberInComplianceStateException($response, previous: $senderException),
                'Forgotten Email Not Subscribed' => new ForgottenEmailNotSubscribedException($response, previous: $senderException),
                default => null,
            };
        }

        return null;
    }

    public function audiences(): AudienceResource
    {
        return new AudienceResource($this);
    }

    public function members(string $list): MemberResource
    {
        return new MemberResource($this, $list);
    }

    public function mergeFields(string $list): MergeFieldResource
    {
        return new MergeFieldResource($this, $list);
    }

    public function webhooks(string $list): WebhookResource
    {
        return new WebhookResource($this, $list);
    }
}
