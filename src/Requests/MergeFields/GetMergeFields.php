<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\MergeFields;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Mailchimp\Data\MergeField;

final class GetMergeFields extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected readonly string $list)
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}/merge-fields";
    }

    /** @return array<int, MergeField> */
    public function createDtoFromResponse(Response $response): array
    {
        /** @var array<int, array<string, mixed>> $fields */
        $fields = $response->json('merge_fields') ?? [];

        return array_map(fn (array $field): MergeField => MergeField::from($field), $fields);
    }
}
