<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Data;

use Sensson\Mailchimp\Enums\MemberStatus;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

final class Member extends Data
{
    /** @param array<string, string>|null $merge_fields */
    public function __construct(
        public string $email_address,
        public ?string $id = null,
        #[WithCast(EnumCast::class, type: MemberStatus::class)]
        public ?MemberStatus $status = null,
        public ?array $merge_fields = null,
        public ?string $unique_email_id = null,
        public ?int $web_id = null,
    ) {
        //
    }

    public function subscriberHash(): string
    {
        return md5(strtolower($this->email_address));
    }
}
