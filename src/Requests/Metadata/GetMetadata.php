<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Metadata;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetMetadata extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $accessToken)
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/oauth2/metadata';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => "OAuth {$this->accessToken}",
        ];
    }
}
