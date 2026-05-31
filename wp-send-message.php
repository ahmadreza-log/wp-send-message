<?php

/**
 * WP Send Message - Main Plugin Bootstrap File
 *
 * This is the entry point for the WordPress plugin. WordPress reads the
 * plugin header below to register the plugin in the admin Plugins screen.
 * All plugin initialization happens through the WpSendMessage singleton class.
 *
 * @package wp-send-message
 * @author  Ahmadreza Ebrahimi
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Send Message
 * Plugin URI:        https://ahmadreza.me/plugins/wp-send-message
 * Description:       Easily send message from you wordpress website via SMS or E-mail.
 * Version:           1.0.0
 * Author:            Ahmadreza Ebrahimi
 * Author URI:        https://ahmadreza.me
 * Requires PHP:      7.4
 * Requires at least: 6.2
 */

// Prevent direct HTTP access to this file outside of WordPress bootstrap.
// ABSPATH is defined by WordPress core when loading through wp-load.php or wp-admin.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin-wide constants only if they have not been defined elsewhere.
// The "defined() || define()" pattern avoids redefinition errors in tests or multisite.

// WPSM_VERSION: Used for cache-busting enqueued assets and version checks.
defined("WPSM_VERSION") || define('WPSM_VERSION', '1.0.0');

// WPSM_FILE: Absolute filesystem path to this main plugin file (used by activation/uninstall hooks).
defined("WPSM_FILE")    || define('WPSM_FILE',    __FILE__);

// WPSM_PATH: Directory path ending with trailing slash, used for require_once and file_exists checks.
defined("WPSM_PATH")    || define('WPSM_PATH',    plugin_dir_path(__FILE__));

// WPSM_URL: Public URL to the plugin directory, used when enqueueing CSS/JS assets.
defined("WPSM_URL")     || define('WPSM_URL',     plugin_dir_url(__FILE__));

// Wrap the main class in class_exists() so the plugin can be safely extended or mocked in tests.
if (!class_exists('WpSendMessage')) {

    /**
     * Main plugin orchestrator class.
     *
     * Uses the Singleton pattern so only one instance exists per request.
     * Responsible for loading dependencies, registering hooks, and coordinating
     * activation/deactivation lifecycle events.
     */
    class WpSendMessage
    {
        /**
         * Human-readable plugin version string.
         * Mirrors WPSM_VERSION constant; kept as a public property for external access.
         *
         * @var string
         */
        public $version = '1.0.0';

        /**
         * Generic storage for runtime plugin data (currently unused, reserved for future use).
         *
         * @var array
         */
        public $data = [];

        /**
         * Holds the single class instance for the Singleton pattern.
         *
         * @var WpSendMessage|null
         */
        private static $instance = null;

        /**
         * Database helper instance for creating/dropping plugin tables.
         *
         * @var WpSendMessage\Includes\DB
         */
        private WpSendMessage\Includes\DB $db;

        /**
         * Asset enqueue helper for registering CSS and JavaScript in the admin area.
         *
         * @var WpSendMessage\Includes\Enqueue
         */
        private WpSendMessage\Includes\Enqueue $enqueue;

        /**
         * Returns the singleton instance, creating it on first call.
         *
         * @return WpSendMessage The single plugin instance.
         */
        public static function instance(): WpSendMessage
        {
            // Lazy initialization: instantiate only when first requested.
            if (is_null(self::$instance)) {
                self::$instance = new WpSendMessage();
            }

            return self::$instance;
        }

        /**
         * Constructor wires up all plugin components and WordPress hooks.
         *
         * Called once by instance(). Must not be invoked directly from outside.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            // Load all PHP class files before instantiating them.
            $this->includes();

            // Instantiate service classes used throughout the plugin lifecycle.
            $this->db = new WpSendMessage\Includes\DB();
            $this->enqueue = new WpSendMessage\Includes\Enqueue();

            // Runs when the plugin is activated from the Plugins screen; creates DB tables.
            register_activation_hook(WPSM_FILE, [$this, 'activate']);

            // Runs when the plugin is deleted via Plugins > Delete; should drop tables and clean options.
            register_uninstall_hook(WPSM_FILE, [$this, 'uninstall']);

            // TEMPORARY: Also drops tables on deactivation — should be removed before production.
            // Todo: Remove This
            register_deactivation_hook(WPSM_FILE, [$this, 'uninstall']);

            // Enqueue JavaScript only on admin pages (not frontend).
            add_action('admin_enqueue_scripts', [$this, 'scripts']);

            // Enqueue CSS only on admin pages.
            add_action('admin_enqueue_scripts', [$this, 'styles']);

            // Bootstrap the admin menu and page rendering on WordPress init.
            add_action('init', ['WpSendMessage\Includes\Admin', 'instance']);

            // Register AJAX handlers for admin-ajax.php requests.
            add_action('init', ['WpSendMessage\Includes\Ajax', 'instance']);
        }

        /**
         * Loads all required PHP class files from the includes/ directory.
         *
         * Uses require_once to prevent duplicate class declarations.
         *
         * @return void
         */
        public function includes(): void
        {
            require_once WPSM_PATH . 'includes/class-db.php';
            require_once WPSM_PATH . 'includes/class-enqueue.php';
            require_once WPSM_PATH . 'includes/class-admin.php';
            require_once WPSM_PATH . 'includes/class-ajax.php';
        }

        /**
         * Plugin activation callback.
         *
         * Creates the contacts table in the WordPress database using dbDelta().
         * Column definitions follow MySQL syntax; NULL allows optional fields.
         *
         * @return void
         */
        public function activate(): void
        {
            $this->db->create('contacts', [
                'name'       => 'VARCHAR(100) NULL',  // Contact display name, max 100 characters.
                'phone'      => 'VARCHAR(15) NULL', // Iranian mobile format: 09xxxxxxxxx (11 digits).
                'email'      => 'VARCHAR(30) NULL', // Email address (note: 30 chars may be too short for some emails).
                'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP', // Auto-set on insert.
            ]);
        }

        /**
         * Plugin uninstall/deactivation callback.
         *
         * Drops the contacts table and removes it from the wpsm_db_tables option.
         * WARNING: Currently also bound to deactivation hook — data loss on deactivate.
         *
         * @return void
         */
        public function uninstall(): void
        {
            $this->db->drop('contacts');
        }

        /**
         * Enqueues admin JavaScript assets via the Enqueue helper.
         *
         * Hooked to admin_enqueue_scripts; runs on every admin page load.
         * UIkit provides modal, notification, and icon components.
         * script.js handles AJAX form submission for the contacts page.
         *
         * @return void
         */
        public function scripts()
        {
            // UIkit core JavaScript (modals, toggles, etc.) — version pinned to 3.25.16.
            $this->enqueue->script('uikit', '3.25.16');

            // UIkit icon set used by uk-icon attributes in admin templates.
            $this->enqueue->script('uikit-icons', '3.25.16');

            // Plugin-specific JS; depends on jQuery for $.ajax and DOM helpers.
            $this->enqueue->script('script', WPSM_VERSION, ['jquery']);
        }

        /**
         * Enqueues admin CSS assets via the Enqueue helper.
         *
         * Loads UIkit stylesheet (RTL variant automatically when is_rtl() is true).
         *
         * @return void
         */
        public function styles()
        {
            $this->enqueue->style('uikit', '3.25.16');
        }
    }

    /**
     * Boot the plugin immediately after this file is loaded by WordPress.
     *
     * WordPress includes active plugin main files during plugins_loaded / init;
     * calling instance() here ensures hooks are registered on every request.
     *
     * @return void
     */
    WpSendMessage::instance();
}
