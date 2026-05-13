<?php

declare(strict_types=1);

use Saloon\Exceptions\Request\ClientException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Connectors\MailchimpConnector;
use Sensson\Mailchimp\Data\Member;
use Sensson\Mailchimp\Enums\MemberStatus;
use Sensson\Mailchimp\Enums\ServerPrefix;
use Sensson\Mailchimp\Exceptions\AccessTokenRevokedException;
use Sensson\Mailchimp\Exceptions\ForgottenEmailNotSubscribedException;
use Sensson\Mailchimp\Exceptions\MemberInComplianceStateException;
use Sensson\Mailchimp\Requests\Audiences\ListAudiences;
use Sensson\Mailchimp\Requests\Members\CreateOrUpdateMember;

it('resolves the base url with the server prefix', function (): void {
    $connector = new MailchimpConnector(ServerPrefix::Us6, 'test-token');

    expect($connector->resolveBaseUrl())->toBe('https://us6.api.mailchimp.com/3.0');
});

it('throws an exception on a 401 response', function (): void {
    $mock = new MockClient([
        ListAudiences::class => MockResponse::make(['detail' => 'API key is invalid'], 401),
    ]);

    $connector = new MailchimpConnector(ServerPrefix::Us6, 'bad-token');
    $connector->withMockClient($mock);

    $connector->audiences()->all();
})->throws(AccessTokenRevokedException::class);

it('throws a typed exception when a member is in a compliance state', function (): void {
    $mock = new MockClient([
        CreateOrUpdateMember::class => MockResponse::make([
            'title' => 'Member In Compliance State',
            'status' => 400,
            'detail' => 'jane@example.com is in a compliance state due to unsubscribe, bounce, or compliance review and cannot be subscribed.',
        ], 400),
    ]);

    $connector = new MailchimpConnector(ServerPrefix::Us6, 'test-token');
    $connector->withMockClient($mock);

    $connector->members('list-1')->createOrUpdate(new Member(
        email_address: 'jane@example.com',
        status: MemberStatus::Subscribed,
    ));
})->throws(MemberInComplianceStateException::class);

it('throws a typed exception when a forgotten email cannot be subscribed', function (): void {
    $mock = new MockClient([
        CreateOrUpdateMember::class => MockResponse::make([
            'title' => 'Forgotten Email Not Subscribed',
            'status' => 400,
            'detail' => 'jane@example.com was permanently deleted and cannot be added back.',
        ], 400),
    ]);

    $connector = new MailchimpConnector(ServerPrefix::Us6, 'test-token');
    $connector->withMockClient($mock);

    $connector->members('list-1')->createOrUpdate(new Member(
        email_address: 'jane@example.com',
        status: MemberStatus::Subscribed,
    ));
})->throws(ForgottenEmailNotSubscribedException::class);

it('throws a generic client exception for other 400 responses', function (): void {
    $mock = new MockClient([
        CreateOrUpdateMember::class => MockResponse::make([
            'title' => 'Invalid Resource',
            'status' => 400,
            'detail' => 'The resource submitted could not be validated.',
        ], 400),
    ]);

    $connector = new MailchimpConnector(ServerPrefix::Us6, 'test-token');
    $connector->withMockClient($mock);

    $connector->members('list-1')->createOrUpdate(new Member(
        email_address: 'jane@example.com',
        status: MemberStatus::Subscribed,
    ));
})->throws(ClientException::class);
