<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Resources;

use Illuminate\Support\Collection;
use Saloon\Http\BaseResource;
use Sensson\Mailchimp\Data\Audience;
use Sensson\Mailchimp\Requests\Audiences\GetAudience;
use Sensson\Mailchimp\Requests\Audiences\ListAudiences;

class AudienceResource extends BaseResource
{
    /** @return Collection<int, Audience> */
    public function all(): Collection
    {
        /** @var array<int, Audience> $audiences */
        $audiences = $this->connector->send(new ListAudiences)->dtoOrFail();

        return collect($audiences);
    }

    public function get(string $id): Audience
    {
        /** @var Audience */
        return $this->connector->send(new GetAudience($id))->dtoOrFail();
    }
}
