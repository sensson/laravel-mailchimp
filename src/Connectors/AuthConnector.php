<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Connectors;

use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Sensson\Mailchimp\Requests\Auth\ExchangeToken;
use Sensson\Mailchimp\Requests\Metadata\GetMetadata;

class AuthConnector extends Connector
{
    use AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return 'https://login.mailchimp.com';
    }

    public function getAuthorizationUrl(?string $redirectUri = null): string
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config()->string('mailchimp.oauth.client_id'),
            'redirect_uri' => $redirectUri ?? config()->string('mailchimp.oauth.redirect_uri'),
        ]);

        return "{$this->resolveBaseUrl()}/oauth2/authorize?{$params}";
    }

    public function exchangeToken(string $code, ?string $redirectUri = null): string
    {
        $request = new ExchangeToken(
            code: $code,
            redirectUri: $redirectUri ?? config()->string('mailchimp.oauth.redirect_uri'),
        );

        /** @var string */
        return $this->send($request)->json('access_token');
    }

    public function getMetadata(string $accessToken): Response
    {
        return $this->send(new GetMetadata($accessToken));
    }
}
