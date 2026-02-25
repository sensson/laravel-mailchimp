<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Mailchimp\Data\Member;

final class ListMembers extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $listId,
        protected readonly ?int $count = null,
        protected readonly ?int $offset = null,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->listId}/members";
    }

    /** @return array<string, int|null> */
    protected function defaultQuery(): array
    {
        return array_filter([
            'count' => $this->count,
            'offset' => $this->offset,
        ], fn (mixed $value): bool => $value !== null);
    }

    /** @return array<int, Member> */
    public function createDtoFromResponse(Response $response): array
    {
        /** @var array<int, array<string, mixed>> $members */
        $members = $response->json('members') ?? [];

        return array_map(fn (array $member): Member => Member::from($member), $members);
    }
}
