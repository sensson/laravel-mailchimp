<?php

declare(strict_types=1);

namespace Sensson\Mailchimp\Requests\Members;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class AddMemberTags extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /** @param array<int, array{name: string, status: string}> $tags */
    public function __construct(
        protected readonly string $list,
        protected readonly string $subscriberHash,
        protected readonly array $tags,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/lists/{$this->list}/members/{$this->subscriberHash}/tags";
    }

    /** @return array<string, array<int, array{name: string, status: string}>> */
    protected function defaultBody(): array
    {
        return [
            'tags' => $this->tags,
        ];
    }
}
