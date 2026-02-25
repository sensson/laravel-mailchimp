<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Sensson\Mailchimp\Data\Member;

class BatchMembers extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, Member>  $members
     */
    public function __construct(
        protected string $listId,
        protected array $members,
        protected bool $updateExisting = true,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->listId}";
    }

    /** @return array<string, array<int, array<string, string|array<string, string>|null>>|bool> */
    protected function defaultBody(): array
    {
        return [
            'members' => array_map(fn (Member $member): array => array_filter([
                'email_address' => $member->email_address,
                'status' => $member->status?->value,
                'merge_fields' => $member->merge_fields,
            ], fn (mixed $value): bool => $value !== null), $this->members),
            'update_existing' => $this->updateExisting,
        ];
    }
}
