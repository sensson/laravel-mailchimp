<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Resources;

use Illuminate\Support\Collection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Mailchimp\Data\Member;
use Sensson\Mailchimp\Requests\Members\ArchiveMember;
use Sensson\Mailchimp\Requests\Members\BatchMembers;
use Sensson\Mailchimp\Requests\Members\CreateOrUpdateMember;
use Sensson\Mailchimp\Requests\Members\GetMember;
use Sensson\Mailchimp\Requests\Members\ListMembers;

class MemberResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        protected string $listId,
    ) {
        parent::__construct($connector);
    }

    /** @return Collection<int, Member> */
    public function all(?int $count = null, ?int $offset = null): Collection
    {
        /** @var array<int, Member> $members */
        $members = $this->connector->send(new ListMembers($this->listId, $count, $offset))->dtoOrFail();

        return collect($members);
    }

    public function get(string $subscriberHash): Member
    {
        /** @var Member */
        return $this->connector->send(new GetMember($this->listId, $subscriberHash))->dtoOrFail();
    }

    public function createOrUpdate(Member $member): Member
    {
        /** @var Member */
        return $this->connector->send(new CreateOrUpdateMember($this->listId, $member))->dtoOrFail();
    }

    public function archive(string $subscriberHash): void
    {
        $this->connector->send(new ArchiveMember($this->listId, $subscriberHash));
    }

    /** @param array<int, Member> $members */
    public function batch(array $members, bool $updateExisting = true): void
    {
        $this->connector->send(new BatchMembers($this->listId, $members, $updateExisting));
    }
}
