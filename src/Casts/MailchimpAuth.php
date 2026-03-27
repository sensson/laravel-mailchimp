<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Sensson\Mailchimp\Data\MailchimpToken;
use Sensson\Mailchimp\Enums\ServerPrefix;

/** @implements CastsAttributes<MailchimpToken, MailchimpToken> */
final readonly class MailchimpAuth implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MailchimpToken
    {
        if (! is_string($value)) {
            return null;
        }

        /** @var array{accessToken: string, serverPrefix: string} $data */
        $data = json_decode($value, true);

        return new MailchimpToken(
            accessToken: $data['accessToken'],
            serverPrefix: ServerPrefix::from($data['serverPrefix']),
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode([
            'accessToken' => $value->accessToken,
            'serverPrefix' => $value->serverPrefix->value,
        ]) ?: null;
    }
}
