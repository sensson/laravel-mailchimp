<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ArchiveMember extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $listId,
        protected string $subscriberHash,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->listId}/members/{$this->subscriberHash}";
    }
}
