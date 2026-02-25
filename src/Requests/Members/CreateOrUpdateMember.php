<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Sensson\Mailchimp\Data\Member;

class CreateOrUpdateMember extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        protected string $listId,
        protected Member $member,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->listId}/members/{$this->member->subscriberHash()}";
    }

    /** @return array<string, string|array<string, string>|null> */
    protected function defaultBody(): array
    {
        return array_filter([
            'email_address' => $this->member->email_address,
            'status' => $this->member->status?->value,
            'merge_fields' => $this->member->merge_fields,
        ], fn (mixed $value): bool => $value !== null);
    }

    public function createDtoFromResponse(Response $response): Member
    {
        return Member::from($response->json());
    }
}
