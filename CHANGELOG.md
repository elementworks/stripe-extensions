# Stripe Extensions Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.2.7 - 2023-11-20
### Changed
- Rename email file

## 1.2.6 - 2023-11-20
### Changed
- Change email notification to fire on `customer.subscription.deleted` Stripe event instead of `customer.subscription.trial_will_end` Stripe event

## 1.2.5 - 2023-10-12
### Changed
- Change Enupal Stripe plugin dependency to ^4.0.0

## 1.2.4 - 2023-10-10
### Fixed
- Fix reference to message constant

## 1.2.3 - 2023-10-10
### Fixed
- Add missing `use` statement

## 1.2.2 - 2023-10-10
### Fixed
- Add missing semicolon

## 1.2.1 - 2023-10-10
### Fixed
- Add missing `use` statement

## 1.2.0 - 2023-10-10
### Added
- Add webhook handler for `customer.subscription.trial_will_end` Stripe event

## 1.1.3 - 2021-03-04
### Changed
- Enable selected user lightswitch field when subscription ends

## 1.1.2 - 2021-03-04
### Fixed
- Add missing `use` statement

## 1.1.1 - 2021-03-04
### Changed
- Update composer requirements for Enupal Stripe Payments plugin

## 1.1.0 - 2021-03-04
### Changed
- Toggle user lightswitch field on subscription end

## 1.0.14 - 2021-02-15
### Fixed
- Fix DateTime namespacing

## 1.0.13 - 2021-02-14
### Fixed
- Fix DateTime formatting

## 1.0.12 - 2021-02-14
### Fixed
- Save the user after setting the subscription expiration date

## 1.0.11 - 2021-02-14
### Fixed
- Fix check field type

## 1.0.10 - 2021-02-14
### Added
- Add webhook handler to update subscription expiry date on user after invoice.paid event

## 1.0.9 - 2021-02-14
### Added
- Add option to set subscription expiry date on user

## 1.0.8 - 2021-02-14
### Added
- Add option to auto login new user after creation

### Changed
- Check for existing user before creating new one

## 1.0.7 - 2021-02-13
### Fixed
- Fix reference to Stripe plugin class

## 1.0.6 - 2021-02-13
### Fixed
- Add use statement to load Craft User element class

## 1.0.5 - 2021-02-13
### Fixed
- Really fix settings

## 1.0.4 - 2021-02-13
### Fixed
- Fix settings

## 1.0.3 - 2021-02-13
### Fixed
- Fix attribute names

## 1.0.2 - 2021-02-13
### Fixed
- Fix namespace

## 1.0.1 - 2021-02-13
### Changed
- Change icon

## 1.0.0 - 2021-02-13
### Added
- Initial release
