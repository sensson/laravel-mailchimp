<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Audiences;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Mailchimp\Data\Audience;

class ListAudiences extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/lists';
    }

    /** @return array<int, Audience> */
    public function createDtoFromResponse(Response $response): array
    {
        /** @var array<int, array<string, mixed>> $lists */
        $lists = $response->json('lists') ?? [];

        return array_map(fn (array $list): Audience => Audience::from($list), $lists);
    }
}
