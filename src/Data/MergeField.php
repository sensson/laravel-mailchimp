<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Data;

use Spatie\LaravelData\Data;

final class MergeField extends Data
{
    public function __construct(
        public int $merge_id,
        public string $tag,
        public string $name,
        public string $type,
        public bool $required,
    ) {
        //
    }
}
