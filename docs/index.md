# omnipulse/liberapay

Liberapay for [omnisense/omnipulse](https://github.com/glitchr-omnisense/omnipulse): an account's
public profile read from `https://liberapay.com/<user>/public.json` - the patrons counted, what it
receives and its goal (by the week, as Liberapay counts), its summary. One `GET`, no key.

## Installation

```sh
composer require omnipulse/liberapay symfony/http-client
```

Requires PHP 8.2, `omnisense/omnipulse` (1.x) and `symfony/http-client-contracts` (^3.0): the
application gives the HTTP client to the factory's constructor. No library is wrapped: there is
none, and the endpoint is one JSON document.

## Credentials

None: `public.json` is public. Liberapay is run by a non-profit on donations: send a
`User-Agent` naming the application (`user_agent`), and cache what is read.

## Options

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `account` | string | - | yes | the username: `Liberapay` |
| `language` | string | `en` | no | the summary's language, when the account wrote several; else the first one |
| `base_url` | string | `https://liberapay.com` | no | |
| `user_agent` | string | `omnipulse (+https://github.com/glitchr-omnisense/omnipulse)` | no | sent with every call |

## Capabilities

| Contract | Supported |
|---|---|
| `ProfileInterface` | yes |
| `SponsorsInterface` | no: Liberapay shows no patron by name |
| `TiersInterface` | no: Liberapay has no tiers |

`capabilities()`: profile only; `key` false; terms: liberapay.com/about/legal and
liberapay.com/about/privacy. `$registry->sponsors('liberapay')` and `tiers('liberapay')` throw
`NotSupportedException`.

## Behaviour

`GET <base_url>/<account>/public.json` with the `User-Agent` and `Accept: application/json`:

- `handle` `username` (else the account asked for), `name` `display_name` (else the username),
  `url` `<base_url>/<username>`, `avatarUrl` `avatar`, `sponsors` `npatrons`;
- `description`: the summary whose `lang` is `language`, else the first summary;
- `income`: `receiving` (`{amount, currency}`, a decimal), by the week; `null` when the account
  hides it;
- `goal`: `goal`, by the week, its `percent` what it receives times 100 over the goal, rounded
  down (`null` when the income is hidden); `null` when there is no goal.

```php
$profile = $registry->profile('liberapay')->profile();
$profile->income->monthly()->decimal();     // "2757.21": 636.28 a week, 52/12 weeks a month
$profile->goal->percent;                    // 63
```

An account hiding what it receives (`receiving: null`, `goal: null`) has `income` and `goal`
`null`, its patrons still counted.

## Example

The core's [installation page](https://github.com/glitchr-omnisense/omnipulse/blob/1.x/docs/installation.md)
has a complete script on this provider, run as written by the core's tests on a recorded answer:

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Omnipulse\Liberapay\LiberapayFactory;
use Omnipulse\Registry;
use Symfony\Component\HttpClient\HttpClient;

$registry = new Registry([new LiberapayFactory(HttpClient::create(['timeout' => 10]))], [
    'liberapay' => ['factory' => 'liberapay', 'options' => ['account' => 'Liberapay', 'user_agent' => 'my-site (+https://example.org)']],
]);
$profile = $registry->profile('liberapay')->profile();

echo $profile->sponsors, " patrons\n";                                                      // 1782 patrons
echo $profile->income?->monthly()?->decimal(), ' ', $profile->income?->amount->currency, " a month\n";   // 2757.21 EUR a month
echo $profile->goal?->percent, "% of the goal\n";                                          // 63% of the goal
```

(the output in the comments is the recorded answer's, from the Liberapay team's public.json of
2026-10-24)

## Errors

- A status of 300 or more: `ProviderException` `Liberapay answered <status> for the account
  <account>.` - an unknown account is a 404.
- A body that is not JSON: `ProviderException`.
- A network failure: `UnreachableException` `Liberapay could not be reached for the account
  <account>: ...`.

## Verified, and not

`Tests/LiberapayProviderTest.php`, on recorded answers (the Liberapay team's public.json of
2026-10-24, shortened): the public profile, an account that hides what it receives, a summary in
another language than asked, an account Liberapay does not know, a profile only. Green in the
family's harness on 2026-10-26. Live without a key (README, 2026-10-24): the Liberapay team's
profile (`Tests/LiveTest.php`, group `live`).

## Responsibility

This package is an independent, unofficial client: it is not affiliated with, endorsed by or
sponsored by Liberapay. Whoever runs it complies with Liberapay's [legal
information](https://liberapay.com/about/legal) and [privacy policy](https://liberapay.com/about/privacy);
see the README's Responsibility section.

## Usage policy

Liberapay's usage policy is declared as data in [`policy.json`](policy.json) (omnisense/contracts'
schema, named in `composer.json`'s `extra.omnisense.policy`), its terms read on
2026-11-10 (https://liberapay.com/about/faq): no rate limit declared, rights unknown: Liberapay publishes no API terms; no key. omnipulse's registry enforces it - a call over a
limit is never made, a 429 and its `Retry-After` hold every next call
(`UsageLimitReachedException`) - and a site may tighten it, never loosen it
(omnisense/omnipulse's docs/policies.md).
