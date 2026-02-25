<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Auth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;
use Stringable;

final class ExchangeToken extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $code,
        protected readonly string $redirectUri,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/oauth2/token';
    }

    /** @return array<string, string|Stringable> */
    protected function defaultBody(): array
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => config()->string('mailchimp.oauth.client_id'),
            'client_secret' => config()->string('mailchimp.oauth.client_secret'),
            'redirect_uri' => $this->redirectUri,
            'code' => $this->code,
        ];
    }
}
