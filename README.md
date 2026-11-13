# omnipulse/liberapay

Liberapay for [omnisense/omnipulse](https://github.com/glitchr-omnisense/omnipulse), under any PHP
host: an account's public profile read from `https://liberapay.com/<user>/public.json` - patrons
counted, what it receives and its goal (by the week, as Liberapay counts), its summary. No key.

Formerly `omnifund/liberapay` (omnifund merged into omnipulse on 2026-11-10: `Omnifund\Liberapay\` is now
`Omnipulse\Liberapay\`; this package replaces the former name).

## Install and a first call

```sh
composer require omnipulse/liberapay
```

No account for this first call: Liberapay's public profile is recorded
(`Omnipulse\Testing\RecordedHttpClient` answers in place of the API), so nothing leaves the process.
A real call needs no account and no key either, only an HTTP client.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Omnipulse\Liberapay\LiberapayFactory;
use Omnipulse\Testing\RecordedHttpClient;

// Liberapay's public profile, recorded: nothing sent. HttpClient::create() (symfony/http-client) for a real call.
$http = RecordedHttpClient::routes([
    'GET /Liberapay/public.json' => '{"username": "Liberapay", "kind": "group", "npatrons": 1782, "receiving": {"amount": "636.28", "currency": "EUR"}, "goal": {"amount": "1000.00", "currency": "EUR"}}',
]);
$liberapay = (new LiberapayFactory($http))->create(['account' => 'Liberapay']);

$profile = $liberapay->profile();
echo $profile->handle, ' ', $profile->sponsors, ' patrons, ', $profile->income?->monthly()?->decimal(), ' ', $profile->income?->monthly()?->currency, " a month\n";   // Liberapay 1782 patrons, 2757.21 EUR a month
```

With your credentials, through the family's registry:

```php
use Omnipulse\Liberapay\LiberapayFactory;
use Omnipulse\Registry;

$registry = new Registry([new LiberapayFactory($httpClient)], [
    'liberapay' => ['factory' => 'liberapay', 'options' => ['account' => 'Liberapay']],
]);
$registry->profile('liberapay')->profile()->income?->monthly();
```

Options: `account` (the username, required), `language` (the summary's, `en`; else the first
one), `base_url`, `user_agent`.

A **profile only**: Liberapay shows no patron by name and has no tiers, so `sponsors()` and
`tiers()` are refused by the registry (`NotSupportedException`). An account may hide what it
receives: `income` and the goal's percentage are then null.

No library is wrapped: there is none, and the endpoint is one JSON document.

## Terms

Liberapay's [legal information](https://liberapay.com/about/legal) and
[privacy policy](https://liberapay.com/about/privacy). Liberapay is run by a non-profit on
donations: send a User-Agent naming your application, and cache what you read (its data changes
weekly).

## Usage policy

Liberapay's usage policy is declared as data in [`policy.json`](policy.json) (omnisense/contracts'
schema, named in `composer.json`'s `extra.omnisense.policy`), its terms read on
2026-11-10 (https://liberapay.com/about/faq): no rate limit declared, rights unknown: Liberapay publishes no API terms; no key. omnipulse's registry enforces it - a call over a
limit is never made, a 429 and its `Retry-After` hold every next call
(`UsageLimitReachedException`) - and a site may tighten it, never loosen it
(omnisense/omnipulse's docs/policies.md).

## Verified, and not

- Verified on recorded answers (the Liberapay team's public.json of 2026-10-24, shortened): the
  profile, a weekly income by the month, the goal's percentage, an account hiding its income, the
  summary's language, an unknown account, the capability refused; `BareTest`; PHPStan at the
  highest level.
- Verified live without a key (2026-10-24): the Liberapay team's profile.

## Responsibility

This package is an independent, unofficial client: it is not affiliated with, endorsed by or
sponsored by Liberapay. Its MIT licence covers this package's code only and grants no right to
the service or its data. Whoever installs and runs it is responsible for complying with
Liberapay's terms and for any use made of what it returns; the authors accept no liability for how
it is used (see [LICENSE](LICENSE)).

## Licence

MIT.
