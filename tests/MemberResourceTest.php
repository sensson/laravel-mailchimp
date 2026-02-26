<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Data\Member;
use Sensson\Mailchimp\Enums\MemberStatus;
use Sensson\Mailchimp\Facades\Mailchimp;
use Sensson\Mailchimp\Requests\Members\AddMemberTags;
use Sensson\Mailchimp\Requests\Members\ArchiveMember;
use Sensson\Mailchimp\Requests\Members\BatchMembers;
use Sensson\Mailchimp\Requests\Members\CreateOrUpdateMember;
use Sensson\Mailchimp\Requests\Members\GetMember;
use Sensson\Mailchimp\Requests\Members\ListMembers;

it('lists all members', function (): void {
    $mock = new MockClient([
        ListMembers::class => MockResponse::make([
            'members' => [
                [
                    'id' => '1',
                    'email_address' => 'john@example.com',
                    'status' => 'subscribed',
                    'unique_email_id' => 'abc',
                ],
                [
                    'id' => '2',
                    'email_address' => 'jane@example.com',
                    'status' => 'pending',
                    'unique_email_id' => 'def',
                ],
            ],
        ]),
    ]);

    Mailchimp::fake($mock);

    $members = Mailchimp::members('list-123')->all();

    expect($members)
        ->toHaveCount(2)
        ->first()->toBeInstanceOf(Member::class);

    expect($members->first())
        ->email_address->toBe('john@example.com')
        ->status->toBe(MemberStatus::Subscribed);

    $mock->assertSent(ListMembers::class);
});

it('lists members with pagination', function (): void {
    $mock = new MockClient([
        ListMembers::class => MockResponse::make([
            'members' => [
                ['id' => '1', 'email_address' => 'john@example.com', 'status' => 'subscribed'],
            ],
        ]),
    ]);

    Mailchimp::fake($mock);

    $members = Mailchimp::members('list-123')->all(count: 10, offset: 20);

    expect($members)->toHaveCount(1);

    $mock->assertSent(function (ListMembers $request): bool {
        return $request->query()->get('count') === 10
            && $request->query()->get('offset') === 20;
    });
});

it('gets a single member', function (): void {
    $mock = new MockClient([
        GetMember::class => MockResponse::make([
            'id' => '1',
            'email_address' => 'john@example.com',
            'status' => 'subscribed',
            'merge_fields' => ['FNAME' => 'John', 'LNAME' => 'Doe'],
            'web_id' => 42,
        ]),
    ]);

    Mailchimp::fake($mock);

    $hash = md5('john@example.com');
    $member = Mailchimp::members('list-123')->get($hash);

    expect($member)
        ->toBeInstanceOf(Member::class)
        ->email_address->toBe('john@example.com')
        ->status->toBe(MemberStatus::Subscribed)
        ->merge_fields->toBe(['FNAME' => 'John', 'LNAME' => 'Doe']);

    $mock->assertSent(GetMember::class);
});

it('creates or updates a member', function (): void {
    $mock = new MockClient([
        CreateOrUpdateMember::class => MockResponse::make([
            'id' => '1',
            'email_address' => 'john@example.com',
            'status' => 'subscribed',
            'merge_fields' => ['FNAME' => 'John'],
        ]),
    ]);

    Mailchimp::fake($mock);

    $member = new Member(
        email_address: 'john@example.com',
        status: MemberStatus::Subscribed,
        merge_fields: ['FNAME' => 'John'],
    );

    $result = Mailchimp::members('list-123')->createOrUpdate($member);

    expect($result)
        ->toBeInstanceOf(Member::class)
        ->email_address->toBe('john@example.com')
        ->status->toBe(MemberStatus::Subscribed);

    $mock->assertSent(function (CreateOrUpdateMember $request): bool {
        $body = $request->body()->all();

        return $body['email_address'] === 'john@example.com'
            && $body['status'] === 'subscribed'
            && $body['merge_fields'] === ['FNAME' => 'John'];
    });
});

it('archives a member', function (): void {
    $mock = new MockClient([
        ArchiveMember::class => MockResponse::make([], 204),
    ]);

    Mailchimp::fake($mock);

    $hash = md5('john@example.com');
    Mailchimp::members('list-123')->archive($hash);

    $mock->assertSent(ArchiveMember::class);
});

it('batches members', function (): void {
    $mock = new MockClient([
        BatchMembers::class => MockResponse::make([
            'new_members' => [],
            'updated_members' => [],
        ]),
    ]);

    Mailchimp::fake($mock);

    $members = [
        new Member(email_address: 'john@example.com', status: MemberStatus::Subscribed),
        new Member(email_address: 'jane@example.com', status: MemberStatus::Subscribed),
    ];

    Mailchimp::members('list-123')->batch($members);

    $mock->assertSent(function (BatchMembers $request): bool {
        $body = $request->body()->all();

        return count($body['members']) === 2
            && $body['update_existing'] === true;
    });
});

it('tags a member', function (): void {
    $mock = new MockClient([
        AddMemberTags::class => MockResponse::make([], 204),
    ]);

    Mailchimp::fake($mock);

    $hash = md5('john@example.com');
    Mailchimp::members('list-123')->tag($hash, ['VIP', 'Early Adopter']);

    $mock->assertSent(function (AddMemberTags $request): bool {
        $body = $request->body()->all();

        return $body['tags'] === [
            ['name' => 'VIP', 'status' => 'active'],
            ['name' => 'Early Adopter', 'status' => 'active'],
        ];
    });
});

it('untags a member', function (): void {
    $mock = new MockClient([
        AddMemberTags::class => MockResponse::make([], 204),
    ]);

    Mailchimp::fake($mock);

    $hash = md5('john@example.com');
    Mailchimp::members('list-123')->untag($hash, ['VIP']);

    $mock->assertSent(function (AddMemberTags $request): bool {
        $body = $request->body()->all();

        return $body['tags'] === [
            ['name' => 'VIP', 'status' => 'inactive'],
        ];
    });
});
