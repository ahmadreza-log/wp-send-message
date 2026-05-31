# Contributing to WP Send Message

Thank you for considering a contribution to **WP Send Message**!

## How to contribute

1. **Fork** the repository on GitHub
2. **Clone** your fork locally
3. **Create a branch** for your change:
   ```bash
   git checkout -b feature/my-feature
   ```
4. **Make your changes** following the guidelines below
5. **Commit** with a clear message
6. **Push** to your fork and open a **Pull Request**

## Development setup

```bash
git clone https://github.com/YOUR_USERNAME/wp-send-message.git
# Copy or symlink into wp-content/plugins/wp-send-message/
# Activate the plugin in WordPress admin
```

## Coding guidelines

### PHP

- Minimum PHP **7.4** — use typed properties where appropriate
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Namespace: `WpSendMessage\Includes\`
- Text domain: `wp-send-message` — wrap all user-facing strings in `__()`, `_e()`, `esc_html__()`, etc.
- Sanitize all input; escape all output
- Verify nonces and check `current_user_can()` for admin actions
- Never commit credentials or API keys

### JavaScript

- Use jQuery (already enqueued in admin)
- Keep plugin logic in `assets/js/script.js`
- Do not modify vendored UIkit files — upgrade the bundle instead

### Database

- Use `$wpdb` with prepared statements for queries
- Schema changes go through `dbDelta()` in the DB class
- Bump plugin version when schema changes require migration

## Commit messages

Use clear, descriptive commit messages:

```
Add contact insert method to DB class

Persist validated contact data from AJAX handler into
send_message_contacts table after successful validation.
```

## Pull Request checklist

- [ ] Code follows WordPress and project conventions
- [ ] No PHP syntax errors (`php -l` on changed files)
- [ ] User-facing strings are translatable
- [ ] Input is sanitized and output is escaped
- [ ] CHANGELOG.md updated (Unreleased section) for user-visible changes
- [ ] README updated if behavior or setup changed

## Reporting bugs

Use the [Bug Report issue template](https://github.com/ahmadreza-log/wp-send-message/issues/new?template=bug_report.yml) and include:

- WordPress version
- PHP version
- Steps to reproduce
- Expected vs actual behavior

## Feature requests

Use the [Feature Request issue template](https://github.com/ahmadreza-log/wp-send-message/issues/new?template=feature_request.yml).

## Questions

Open a [GitHub Discussion](https://github.com/ahmadreza-log/wp-send-message/discussions) or contact the author via [ahmadreza.me](https://ahmadreza.me).
