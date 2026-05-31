# WP Send Message

WordPress plugin to send messages via SMS or E-mail from your website admin panel.

## Requirements

- WordPress 6.2+
- PHP 7.4+

## Installation

1. Clone or download this repository into `wp-content/plugins/wp-send-message/`
2. Activate **Wordpress Send Message** from the WordPress **Plugins** screen
3. Go to **Send Message → Contacts** in the admin menu

## Development

```bash
# Clone the repository
git clone <your-repo-url> wp-send-message

# Copy into WordPress plugins directory
cp -r wp-send-message /path/to/wordpress/wp-content/plugins/
```

## Project Structure

```
wp-send-message/
├── wp-send-message.php      # Main plugin bootstrap
├── includes/
│   ├── class-db.php         # Database table management
│   ├── class-admin.php      # Admin menus & pages
│   ├── class-ajax.php       # AJAX handlers
│   ├── class-enqueue.php    # Asset loading
│   └── admin/pages/         # Admin page templates
└── assets/
    ├── css/                 # UIkit stylesheets
    └── js/                  # UIkit + plugin scripts
```

## Author

[Ahmadreza Ebrahimi](https://ahmadreza.me)

## License

GPL v2 or later
