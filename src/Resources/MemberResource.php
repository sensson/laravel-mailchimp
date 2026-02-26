<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Resources;

use Illuminate\Support\Collection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Mailchimp\Data\Member;
use Sensson\Mailchimp\Requests\Members\AddMemberTags;
use Sensson\Mailchimp\Requests\Members\ArchiveMember;
use Sensson\Mailchimp\Requests\Members\BatchMembers;
use Sensson\Mailchimp\Requests\Members\CreateOrUpdateMember;
use Sensson\Mailchimp\Requests\Members\GetMember;
use Sensson\Mailchimp\Requests\Members\ListMembers;

final class MemberResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        protected readonly string $list,
    ) {
        parent::__construct($connector);
    }

    /** @return Collection<int, Member> */
    public function all(?int $count = null, ?int $offset = null): Collection
    {
        /** @var array<int, Member> $members */
        $members = $this->connector->send(new ListMembers($this->list, $count, $offset))->dtoOrFail();

        return collect($members);
    }

    public function get(string $subscriberHash): Member
    {
        /** @var Member */
        return $this->connector->send(new GetMember($this->list, $subscriberHash))->dtoOrFail();
    }

    public function createOrUpdate(Member $member): Member
    {
        /** @var Member */
        return $this->connector->send(new CreateOrUpdateMember($this->list, $member))->dtoOrFail();
    }

    public function archive(string $subscriberHash): void
    {
        $this->connector->send(new ArchiveMember($this->list, $subscriberHash));
    }

    /** @param array<int, Member> $members */
    public function batch(array $members, bool $updateExisting = true): void
    {
        $this->connector->send(new BatchMembers($this->list, $members, $updateExisting));
    }

    /** @param array<int, string> $tags */
    public function tag(string $subscriberHash, array $tags): void
    {
        $this->connector->send(new AddMemberTags(
            $this->list,
            $subscriberHash,
            array_map(fn (string $tag): array => ['name' => $tag, 'status' => 'active'], $tags),
        ));
    }

    /** @param array<int, string> $tags */
    public function untag(string $subscriberHash, array $tags): void
    {
        $this->connector->send(new AddMemberTags(
            $this->list,
            $subscriberHash,
            array_map(fn (string $tag): array => ['name' => $tag, 'status' => 'inactive'], $tags),
        ));
    }
}
