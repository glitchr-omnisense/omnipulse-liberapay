# Changelog

## Unreleased (1.x)

### Added
- Liberapay's usage policy declared as data: `policy.json` (omnisense/contracts' schema), its terms
  read on 2026-11-10, named in `composer.json` (`extra.omnisense.policy`, and
  `omnisense/contracts` required). The provider implements `DeclaresUsagePolicyInterface` and gives
  the policy to its Capabilities; the factory hands the provider's Backoff to its HTTP calls, so
  that a 429 and its `Retry-After` hold every next call. `Tests/PolicyTest.php`; `Tests/BareTest.php`
  allows omnisense/contracts' files, now required.

### Changed
- `omnifund/liberapay` is now `omnipulse/liberapay` (2026-11-10: omnireview and omnifund merged into omnipulse,
  Marco's decision; this repository's history kept, from omnifund-liberapay at 30d7434): the namespace
  `Omnipulse\Liberapay`, requiring `omnisense/omnipulse`, whose one Capabilities and Registry serve the
  funding contracts beside the audience's and the reviews'; `composer.json` replaces
  `omnifund/liberapay`, so that a project never installs both. The factory's name is unchanged (`liberapay`).
  A Registry's unknown provider is now `No "x" provider` (was "funding provider").
