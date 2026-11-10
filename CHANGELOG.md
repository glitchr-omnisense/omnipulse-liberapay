# Changelog

## Unreleased (1.x)

### Changed
- `omnifund/liberapay` is now `omnipulse/liberapay` (2026-11-10: omnireview and omnifund merged into omnipulse,
  Marco's decision; this repository's history kept, from omnifund-liberapay at 30d7434): the namespace
  `Omnipulse\Liberapay`, requiring `omnisense/omnipulse`, whose one Capabilities and Registry serve the
  funding contracts beside the audience's and the reviews'; `composer.json` replaces
  `omnifund/liberapay`, so that a project never installs both. The factory's name is unchanged (`liberapay`).
  A Registry's unknown provider is now `No "x" provider` (was "funding provider").
