# Changelog

All notable changes to `laravel-maib` are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-09-03

### Added
- `MaibCardSaved` event, dispatched when a card is registered without an
  immediate charge (`savecard-recurring` / `savecard-oneclick` without an
  amount). The callback carries `status: PENDING` together with the `billerId`
  to store for the customer.
- Documentation for Apple Pay & Google Pay, which are offered automatically on
  the hosted checkout page and flow through the existing endpoints — no extra
  integration required.
- A 3-D Secure section in the README explaining that fulfilment is decided by
  `status`, not by `threeDs`.

### Changed
- A callback with `status: PENDING` **and** a `billerId` now dispatches
  `MaibCardSaved` instead of `MaibPaymentFailed`, so a successful card
  registration is no longer reported as a failure.
- Callback enum fields (`status`, `threeDs`, `currency`) are parsed with
  `tryFrom()`. A future undocumented value now degrades to `null` instead of
  throwing, preventing a `500` on a signature-valid callback (which would
  otherwise trigger maib's retry loop). As a result, `CallbackPayload::$status`
  and `CallbackPayload::$currency` are now nullable.

## [1.0.0] - Initial release

### Added
- Full coverage of the maib e-Commerce API: direct payment, two-step
  (hold → capture), recurring, one-click, refund, payment info, and saved-card
  deletion.
- Automatic Bearer token generation, caching, and refresh.
- Callback webhook handling with HMAC-SHA256 signature validation and
  `MaibPaymentReceived` / `MaibPaymentFailed` events.
- PHPUnit test suite.

[1.1.0]: https://github.com/Nikba-Creative-Studio/Laravel-Maib/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/Nikba-Creative-Studio/Laravel-Maib/releases/tag/v1.0.0
