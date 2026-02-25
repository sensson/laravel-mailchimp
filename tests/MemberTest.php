<?php

declare(strict_types=1);

use Sensson\Mailchimp\Data\Member;

it('calculates the subscriber hash', function (): void {
    $member = new Member(id: '1', email_address: 'John@Example.com');

    expect($member->subscriberHash())->toBe(md5('john@example.com'));
});

it('lowercases the email before hashing', function (): void {
    $member = new Member(id: '1', email_address: 'USER@EXAMPLE.COM');

    expect($member->subscriberHash())->toBe(md5('user@example.com'));
});
