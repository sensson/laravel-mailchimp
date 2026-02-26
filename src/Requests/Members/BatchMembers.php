<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Sensson\Mailchimp\Data\Member;

final class BatchMembers extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, Member>  $members
     */
    public function __construct(
        protected readonly string $list,
        protected readonly array $members,
        protected readonly bool $updateExisting = true,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}";
    }

    /** @return array<string, array<int, array<string, string|object>>|bool> */
    protected function defaultBody(): array
    {
        return [
            'members' => array_map(fn (Member $member): array => array_filter([
                'email_address' => $member->email_address,
                'status' => $member->status?->value,
                'status_if_new' => $member->status_if_new?->value,
                'merge_fields' => (object) $member->merge_fields,
                'language' => $member->language,
            ], fn (mixed $value): bool => $value !== null), $this->members),
            'update_existing' => $this->updateExisting,
        ];
    }
}
