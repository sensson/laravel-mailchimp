<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Enums;

enum MemberStatus: string
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
    case Cleaned = 'cleaned';
    case Pending = 'pending';
    case Transactional = 'transactional';
    case Archived = 'archived';
}
