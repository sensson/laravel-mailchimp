<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Resources;

use Illuminate\Support\Collection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Mailchimp\Data\MergeField;
use Sensson\Mailchimp\Requests\MergeFields\GetMergeFields;

final class MergeFieldResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        protected readonly string $listId,
    ) {
        parent::__construct($connector);
    }

    /** @return Collection<int, MergeField> */
    public function all(): Collection
    {
        /** @var array<int, MergeField> $fields */
        $fields = $this->connector->send(new GetMergeFields($this->listId))->dtoOrFail();

        return collect($fields);
    }
}
