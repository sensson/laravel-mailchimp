<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Data;

use Sensson\Mailchimp\Enums\ServerPrefix;

final readonly class MailchimpToken
{
    public function __construct(
        public string $accessToken,
        public ServerPrefix $serverPrefix,
    ) {
        //
    }
}
