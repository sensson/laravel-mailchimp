<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Data;

use Spatie\LaravelData\Data;

class Audience extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public int $member_count,
        public ?int $web_id = null,
    ) {
        //
    }
}
