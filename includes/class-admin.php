<?php

/**
 * WordPress admin area integration for WP Send Message.
 *
 * Registers the top-level admin menu, submenu pages, and renders
 * page templates from includes/admin/pages/{slug}.php.
 */

namespace WpSendMessage\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Admin
 *
 * Singleton responsible for all wp-admin menu registration and page routing.
 * Only users with 'manage_options' capability can access plugin pages.
 */
class Admin
{
    /**
     * Singleton instance holder.
     *
     * @var Admin|null
     */
    private static $instance = null;

    /**
     * Base slug used as the parent menu identifier and page query param prefix.
     * Example URLs: admin.php?page=wpsm-contacts
     *
     * @var string
     */
    private string $prefix = 'wpsm';

    /**
     * Map of submenu slug => translated display label.
     * Each key corresponds to a file at includes/admin/pages/{key}.php.
     *
     * @var array
     */
    private array $menus = [];

    /**
     * Returns the singleton Admin instance.
     *
     * @return Admin
     */
    public static function instance(): Admin
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Defines submenu pages and hooks into WordPress admin_menu action.
     *
     * @return void
     */
    public function __construct()
    {
        // Register available admin subpages; 'options' page file does not exist yet.
        $this->menus = [
            'contacts' =>  __('Contacts', 'wp-send-message'), // Contact list and add form
            'options'  => __('Options', 'wp-send-message'),  // Plugin settings (planned)
        ];

        // Register menus when WordPress builds the admin sidebar.
        add_action('admin_menu', [$this, 'menus']);
    }

    /**
     * Creates the top-level menu and all submenu entries in wp-admin.
     *
     * @return void
     */
    public function menus(): void
    {
        // Top-level menu item in the admin sidebar.
        add_menu_page(
            __('Send Message', 'wp-send-message'), // Browser tab / page title
            __('Send Message', 'wp-send-message'), // Sidebar menu label
            'manage_options',                      // Required capability (typically administrators)
            $this->prefix,                         // Menu slug: 'wpsm'
            [$this, 'callback'],                   // Render callback for this page
            'dashicons-email-alt2',                // WordPress Dashicon class
            90                                     // Menu position (lower = higher in sidebar)
        );

        // Position counter for submenu ordering (WordPress uses increments of 10 by convention).
        $i = 10;

        // Register each entry from $this->menus as a submenu under the parent 'wpsm' menu.
        foreach ($this->menus as $slug => $name) {
            add_submenu_page(
                $this->prefix,              // Parent menu slug
                $name,                        // Page title shown in browser tab
                $name,                        // Submenu label in sidebar
                'manage_options',             // Required capability
                $this->prefix . '-' . $slug,  // Full page slug: e.g. 'wpsm-contacts'
                [$this, 'callback'],          // Same callback resolves template by slug
                $i                            // Submenu sort order
            );
            $i += 10;
        }

        // Remove the auto-generated duplicate submenu that mirrors the parent page title.
        // Without this, 'Send Message' would appear twice in the submenu list.
        remove_submenu_page($this->prefix, $this->prefix);
    }

    /**
     * Renders the active admin page by loading the matching template file.
     *
     * Derives the template slug from $_GET['page'] by stripping the 'wpsm-' prefix.
     * Falls back to an error notice if the template file does not exist.
     *
     * @return void
     */
    public function callback(): void
    {
        // Read the current admin page slug from the URL query string.
        $page = $_GET['page'];

        // Convert 'wpsm-contacts' → 'contacts' for template filename lookup.
        if (isset($page) && str_starts_with($page, $this->prefix)) {
            $page = str_replace($this->prefix . '-', '', $page);
        }

        // Open WordPress admin wrapper; CSS class enables JS scoping via .wp-send-message-page.
        echo '<div class="wrap wp-send-message-page wp-send-message-page-' . $page . '">';

        // Output the page title set by add_menu_page / add_submenu_page.
        echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';

        // Load page-specific template if the PHP file exists in includes/admin/pages/.
        if (file_exists(WPSM_PATH . 'includes/admin/pages/' . $page . '.php')) {
            require_once WPSM_PATH . 'includes/admin/pages/' . $page . '.php';
        } else {
            // Graceful fallback for pages under development (e.g. 'options').
            echo '<div class="notice notice-error">';
            echo '<h2>' . esc_html__('Page not found', 'wp-send-message') . '</h2>';
            echo '<p>' . esc_html__('The page you are looking for does not exist or is under development.', 'wp-send-message') . '</p>';
            echo '<p>' . esc_html__('If the problem persists, please contact the developer.', 'wp-send-message') . '</p>';
            echo '</div>';
        }

        // Close the admin page wrapper div.
        echo '</div>';
    }
}
