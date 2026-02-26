<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Data;

use Spatie\LaravelData\Data;

final class Webhook extends Data
{
    public function __construct(
        public string $id,
        public string $url,
        public ?string $list_id = null,
    ) {
        //
    }
}
