<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Audiences;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Mailchimp\Data\Audience;

class GetAudience extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $id)
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->id}";
    }

    public function createDtoFromResponse(Response $response): Audience
    {
        return Audience::from($response->json());
    }
}
