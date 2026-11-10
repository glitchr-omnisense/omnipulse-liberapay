<?php

namespace Omnipulse\Liberapay;

use Omnipulse\Config;
use Omnipulse\ProviderFactory;
use Omnipulse\ProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Liberapay, from its options:
 *
 *   account     the username: "Liberapay" (required)
 *   language    the summary's language, when the account wrote several (en; else the first one)
 *   base_url    https://liberapay.com
 *   user_agent  sent with every call
 */
final class LiberapayFactory extends ProviderFactory
{
    public function __construct(private readonly HttpClientInterface $http)
    {
    }

    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnipulse.factory_name' => 'liberapay',
            'omnipulse.factory_title' => 'Liberapay',
            'omnipulse.required_options' => ['account'],
            'language' => 'en',
            'base_url' => LiberapayProvider::BASE_URL,
            'user_agent' => 'omnipulse (+https://github.com/glitchr-omnisense/omnipulse)',
        ]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new LiberapayProvider($this->http, (string) $c->string('account'), $c->string('language') ?? 'en', $c->string('base_url') ?? LiberapayProvider::BASE_URL, (string) $c->string('user_agent'));
    }
}
