<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Enums;

enum WebhookEvent: string
{
    case Subscribe = 'subscribe';
    case Unsubscribe = 'unsubscribe';
    case Profile = 'profile';
    case Cleaned = 'cleaned';
    case Upemail = 'upemail';
    case Campaign = 'campaign';
    case SmsSubscribe = 'sms_subscribe';
    case SmsUnsubscribe = 'sms_unsubscribe';
    case Upsms = 'upsms';
    case SmsCampaign = 'sms_campaign';
}
