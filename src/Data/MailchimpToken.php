<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Data;

use Sensson\Mailchimp\Enums\ServerPrefix;
use Spatie\LaravelData\Data;

final class MailchimpToken extends Data
{
    public function __construct(
        public string $accessToken,
        public ServerPrefix $serverPrefix,
    ) {
        //
    }
}
