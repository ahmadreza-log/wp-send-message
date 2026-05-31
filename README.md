# WP Send Message

[![WordPress](https://img.shields.io/badge/WordPress-6.2%2B-blue?logo=wordpress&logoColor=white)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange)](https://github.com/ahmadreza-log/wp-send-message/releases)

A WordPress plugin to send messages via **SMS** and **E-mail** directly from your website admin panel. Manage contacts, validate Iranian mobile numbers, and prepare bulk messaging — all inside WordPress.

> **Status:** Early development — contact management UI and validation are in place; message sending and full CRUD are on the roadmap.

---

## Features

### Available now

- Admin menu: **Send Message → Contacts**
- Add contact form with UIkit modal (AJAX, no page reload)
- Server-side validation (name, phone, email)
- Iranian mobile number normalization (`+98`, `0098`, `98`, `9…` → `09xxxxxxxxx`)
- Custom database table for contacts (`{prefix}send_message_contacts`)
- RTL-ready admin UI (UIkit 3.25.16)
- Namespaced PHP architecture with Singleton pattern

### Planned

- Persist contacts to database (insert on form submit)
- Contact list, edit, and delete
- Plugin settings page (SMS gateway, SMTP)
- Send SMS via third-party API
- Send E-mail via `wp_mail()`
- Bulk messaging to selected contacts

---

## Requirements

| Requirement | Version |
|-------------|---------|
| WordPress   | 6.2+    |
| PHP         | 7.4+    |
| MySQL       | 5.7+ / MariaDB 10.3+ |

---

## Installation

### From GitHub

```bash
cd wp-content/plugins
git clone https://github.com/ahmadreza-log/wp-send-message.git
```

Then activate **Wordpress Send Message** under **Plugins** in your WordPress admin.

### Manual

1. Download or clone this repository
2. Copy the `wp-send-message` folder to `wp-content/plugins/`
3. Activate the plugin from **Plugins → Installed Plugins**
4. Open **Send Message → Contacts** in the admin sidebar

---

## Usage

1. Go to **Send Message → Contacts**
2. Click **Add Contact**
3. Fill in **Name** (required) and at least one of **Phone** or **Email**
4. Submit — validation runs via AJAX; errors appear as UIkit notifications

**Phone format (Iran):** accepts `09123456789`, `+989123456789`, `989123456789`, etc.

---

## Development

### Project structure

```
wp-send-message/
├── wp-send-message.php           # Plugin bootstrap & lifecycle hooks
├── includes/
│   ├── class-db.php              # Table create/drop via dbDelta
│   ├── class-admin.php           # Admin menus & page routing
│   ├── class-ajax.php            # AJAX handlers (add-contact)
│   ├── class-enqueue.php         # CSS/JS asset loading
│   └── admin/pages/
│       └── contacts.php          # Contacts admin template
├── assets/
│   ├── css/                      # UIkit stylesheets (+ RTL)
│   └── js/
│       ├── script.js             # Plugin admin JavaScript
│       └── uikit*.js             # UIkit (vendored)
├── .github/                      # Issue/PR templates & CI
├── LICENSE
├── CHANGELOG.md
└── CONTRIBUTING.md
```

### Local setup

```bash
git clone https://github.com/ahmadreza-log/wp-send-message.git
# Place inside your WordPress plugins directory, then activate in admin
```

### Coding standards

- PHP: WordPress Coding Standards, typed properties (PHP 7.4+)
- Text domain: `wp-send-message`
- Namespace: `WpSendMessage\Includes\`

---

## Database

On activation, the plugin creates:

**Table:** `{wpdb_prefix}send_message_contacts`

| Column     | Type           | Notes                    |
|------------|----------------|--------------------------|
| ID         | BIGINT         | Primary key, auto increment |
| name       | VARCHAR(100)   | Contact name             |
| phone      | VARCHAR(15)    | Normalized mobile        |
| email      | VARCHAR(30)    | Email address            |
| created_at | DATETIME       | Default: current timestamp |

---

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes
4. Push and open a Pull Request

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

---

## Author

**Ahmadreza Ebrahimi**

- Website: [ahmadreza.me](https://ahmadreza.me)
- GitHub: [@ahmadreza-log](https://github.com/ahmadreza-log)

---

## License

This project is licensed under the **GNU General Public License v2.0 or later**.

See [LICENSE](LICENSE) for the full license text.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
