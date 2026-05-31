# Changelog

All notable changes to **WP Send Message** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned

- Save contacts to database on form submit
- Display contacts list from database
- Edit and delete contacts
- Options page (SMS gateway, SMTP settings)
- Send SMS and E-mail functionality
- Bulk messaging

## [1.0.0] - 2026-05-31

### Added

- Initial plugin release
- Singleton bootstrap class with namespaced architecture
- Admin menu: Send Message → Contacts, Options
- Contacts page with UIkit modal add-contact form
- AJAX form submission with nonce verification
- Server-side validation (name, phone, email)
- Iranian mobile number normalization and validation
- Database layer with `dbDelta` table creation
- Contacts table: `{prefix}send_message_contacts`
- UIkit 3.25.16 assets (CSS, JS, icons, RTL support)
- Comprehensive inline code documentation
- GitHub repository files (README, LICENSE, CONTRIBUTING, CI)

[Unreleased]: https://github.com/ahmadreza-log/wp-send-message/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/ahmadreza-log/wp-send-message/releases/tag/v1.0.0
