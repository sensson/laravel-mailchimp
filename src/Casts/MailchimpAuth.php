<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Sensson\Mailchimp\Enums\ServerPrefix;

/** @implements CastsAttributes<object{accessToken: string, serverPrefix: ServerPrefix}, object{accessToken: string, serverPrefix: ServerPrefix}> */
class MailchimpAuth implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?object
    {
        if (! is_string($value)) {
            return null;
        }

        /** @var object{accessToken: string, serverPrefix: string} $decoded */
        $decoded = json_decode($value);

        return (object) [
            'accessToken' => $decoded->accessToken,
            'serverPrefix' => ServerPrefix::from($decoded->serverPrefix),
        ];
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
