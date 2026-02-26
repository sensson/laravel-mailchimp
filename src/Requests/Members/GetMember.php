<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Mailchimp\Data\Member;

final class GetMember extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $list,
        protected readonly string $subscriberHash,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}/members/{$this->subscriberHash}";
    }

    public function createDtoFromResponse(Response $response): Member
    {
        return Member::from($response->json());
    }
}
