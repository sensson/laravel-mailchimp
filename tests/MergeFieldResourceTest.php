<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Data\MergeField;
use Sensson\Mailchimp\Facades\Mailchimp;
use Sensson\Mailchimp\Requests\MergeFields\GetMergeFields;

it('lists all merge fields', function (): void {
    $mock = new MockClient([
        GetMergeFields::class => MockResponse::make([
            'merge_fields' => [
                ['merge_id' => 1, 'tag' => 'FNAME', 'name' => 'First Name', 'type' => 'text', 'required' => false],
                ['merge_id' => 2, 'tag' => 'LNAME', 'name' => 'Last Name', 'type' => 'text', 'required' => false],
                ['merge_id' => 3, 'tag' => 'COMPANY', 'name' => 'Company', 'type' => 'text', 'required' => true],
            ],
        ]),
    ]);

    Mailchimp::fake($mock);

    $fields = Mailchimp::mergeFields('list-123')->all();

    expect($fields)
        ->toHaveCount(3)
        ->first()->toBeInstanceOf(MergeField::class);

    expect($fields->first())
        ->merge_id->toBe(1)
        ->tag->toBe('FNAME')
        ->name->toBe('First Name')
        ->type->toBe('text')
        ->required->toBeFalse();

    $mock->assertSent(GetMergeFields::class);
});
