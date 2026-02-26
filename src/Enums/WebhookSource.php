<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Enums;

enum WebhookSource: string
{
    case User = 'user';
    case Admin = 'admin';
    case Api = 'api';
}
