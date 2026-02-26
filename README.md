# Laravel Mailchimp

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sensson/laravel-mailchimp.svg?style=flat-square)](https://packagist.org/packages/sensson/laravel-mailchimp)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sensson/laravel-mailchimp/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sensson/laravel-mailchimp/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sensson/laravel-mailchimp/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sensson/laravel-mailchimp/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sensson/laravel-mailchimp.svg?style=flat-square)](https://packagist.org/packages/sensson/laravel-mailchimp)

A Laravel package for the Mailchimp Marketing API with OAuth 2.0 support, built on [SaloonPHP](https://docs.saloon.dev).

## Installation

```bash
composer require sensson/laravel-mailchimp
```

Publish the config file:

```bash
php artisan vendor:publish --tag="mailchimp-config"
```

Add your OAuth credentials to `.env`. You can create an OAuth app in [your Mailchimp account](https://admin.mailchimp.com/account/oauth2/) under **Registered Apps**:

```env
MAILCHIMP_CLIENT_ID=your-client-id
MAILCHIMP_CLIENT_SECRET=your-client-secret
MAILCHIMP_REDIRECT_URI=https://your-app.com/mailchimp/callback
```

## OAuth 2.0

Redirect the user to Mailchimp to authorize your app:

```php
use Sensson\Mailchimp\Facades\Mailchimp;

return redirect()->to(Mailchimp::auth()->getAuthorizationUrl());
```

Handle the callback to get an access token and data center:

```php
use Sensson\Mailchimp\Enums\ServerPrefix;

$token = Mailchimp::auth()->exchangeToken($request->code);
$metadata = Mailchimp::auth()->getMetadata($token);
$dc = ServerPrefix::from($metadata->json('dc'));
```

Store `$token` and `$dc` for later use. The `MailchimpAuth` cast makes this easy on any Eloquent model:

```php
use Sensson\Mailchimp\Casts\MailchimpAuth;

protected $casts = [
    'mailchimp' => MailchimpAuth::class,
];
```

```php
$user->mailchimp = (object) ['accessToken' => $token, 'serverPrefix' => $dc];
$user->save();
```

## Usage

```php
use Sensson\Mailchimp\Facades\Mailchimp;

$mailchimp = Mailchimp::make($user->mailchimp->serverPrefix, $user->mailchimp->accessToken);
```

### Audiences

```php
$audiences = $mailchimp->audiences()->all();

$audience = $mailchimp->audiences()->get('list-id');
```

### Members

```php
use Sensson\Mailchimp\Data\Member;
use Sensson\Mailchimp\Enums\MemberStatus;

$members = $mailchimp->members('list-id')->all(count: 50, offset: 0);

$member = $mailchimp->members('list-id')->get($subscriberHash);
```

Create or update a member:

```php
$member = new Member(
    email_address: 'john@example.com',
    status: MemberStatus::Subscribed,
    merge_fields: ['FNAME' => 'John', 'LNAME' => 'Doe'],
);

$mailchimp->members('list-id')->createOrUpdate($member);
```

Archive a member:

```php
$hash = md5(strtolower('john@example.com'));

$mailchimp->members('list-id')->archive($hash);
```

Batch subscribe multiple members at once:

```php
$members = [
    new Member(email_address: 'john@example.com', status: MemberStatus::Subscribed),
    new Member(email_address: 'jane@example.com', status: MemberStatus::Subscribed),
];

$mailchimp->members('list-id')->batch($members);
```

Tag and untag members:

```php
$hash = md5(strtolower('john@example.com'));

$mailchimp->members('list-id')->tag($hash, ['VIP', 'Early Adopter']);
$mailchimp->members('list-id')->untag($hash, ['VIP']);
```

### Merge Fields

```php
$fields = $mailchimp->mergeFields('list-id')->all();
```

### Webhooks

```php
use Sensson\Mailchimp\Enums\WebhookEvent;
use Sensson\Mailchimp\Enums\WebhookSource;

$webhooks = $mailchimp->webhooks('list-id')->all();

$webhook = $mailchimp->webhooks('list-id')->create(
    'https://example.com/webhook',
    [WebhookEvent::Subscribe, WebhookEvent::Unsubscribe],
    [WebhookSource::User, WebhookSource::Admin],
);

$mailchimp->webhooks('list-id')->delete($webhook->id);
```

## Testing

Use `fake()` and `authFake()` with Saloon's `MockClient`:

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Mailchimp\Facades\Mailchimp;
use Sensson\Mailchimp\Requests\Audiences\ListAudiences;

$mock = new MockClient([
    ListAudiences::class => MockResponse::make([
        'lists' => [
            ['id' => 'abc123', 'name' => 'Newsletter', 'member_count' => 100],
        ],
    ]),
]);

Mailchimp::fake($mock);

$audiences = Mailchimp::audiences()->all();

$mock->assertSent(ListAudiences::class);
```

Run the test suite:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sensson](https://github.com/sensson)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
