<?php

/**
 * Asset enqueue helper for WP Send Message admin scripts and styles.
 *
 * Resolves file paths based on RTL locale and WP_DEBUG mode,
 * then registers assets through wp_enqueue_script() / wp_enqueue_style().
 */

namespace WpSendMessage\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Enqueue
 *
 * Centralizes asset registration logic so the main plugin class
 * does not need to call wp_enqueue_* directly.
 */
class Enqueue
{
    /**
     * When true, looks for non-minified asset files (currently concatenated into filename — see path()).
     *
     * @var bool
     */
    private bool $debug = false;

    /**
     * Handle prefix for wp_enqueue_* identifiers (e.g. 'wpsm-uikit').
     *
     * @var string
     */
    private string $prefix;

    /**
     * Default version string for cache busting when no explicit version is passed.
     *
     * @var string
     */
    private string $version;

    /**
     * Initializes enqueue settings from WordPress constants and plugin version.
     *
     * @return void
     */
    public function __construct()
    {
        // All registered handles will be prefixed to avoid conflicts with other plugins/themes.
        $this->prefix = 'wpsm-';

        // WP_DEBUG controls whether unminified source files are preferred.
        $this->debug = WP_DEBUG;

        // Fallback version from plugin constant defined in wp-send-message.php.
        $this->version = WPSM_VERSION;
    }

    /**
     * Resolves the public URL for a CSS or JS asset if the file exists on disk.
     *
     * For styles: checks assets/css/{name}{-rtl}{debug}.css
     * For scripts: checks assets/js/{name}{debug}.js
     *
     * @param string $name Asset base filename without extension (e.g. 'uikit', 'script').
     * @param string $type Either 'style' or 'script'.
     * @return string|null Full URL to the asset, or null if file not found.
     */
    private function path(string $name, string $type): ?string
    {
        // Append '-rtl' suffix for right-to-left locales (Persian, Arabic, etc.).
        $rtl = is_rtl() ? '-rtl' : '';

        if ($type == 'style') {
            // Build filesystem path and return URL only if the CSS file exists.
            // NOTE: When $this->debug is true, boolean concatenates as "1" — may break path resolution.
            if (file_exists(WPSM_PATH . 'assets/css/' . $name . $rtl . $this->debug . '.css')) {
                return WPSM_URL . 'assets/css/' . $name . $rtl . $this->debug . '.css';
            }
        } else {
            // Same logic for JavaScript files under assets/js/.
            if (file_exists(WPSM_PATH . 'assets/js/' . $name . $this->debug . '.js')) {
                return WPSM_URL . 'assets/js/' . $name . $this->debug . '.js';
            }
        }

        // Asset file not found — caller will skip enqueueing.
        return null;
    }

    /**
     * Registers and enqueues a stylesheet in the WordPress admin.
     *
     * Silently skips if the resolved file path does not exist.
     *
     * @param string      $name    Asset base name (e.g. 'uikit').
     * @param string|null $version Optional version for cache busting; defaults to plugin version.
     * @param array       $deps    Array of stylesheet handles this file depends on.
     * @return void
     */
    public function style(string $name, ?string $version = null, array $deps = []): void
    {
        // Use plugin version when no explicit version argument is provided.
        if ($version == null) {
            $version = $this->version;
        }

        // Only enqueue if the physical file was found by path().
        if ($this->path($name, 'style') !== null) {
            wp_enqueue_style(
                $this->prefix . $name,   // Unique handle: e.g. wpsm-uikit
                $this->path($name, 'style'), // Public URL to the CSS file
                $deps,                   // Dependencies (other stylesheets to load first)
                $version                 // Query string ?ver= for browser cache invalidation
            );
        }
    }

    /**
     * Registers and enqueues a JavaScript file in the WordPress admin footer.
     *
     * Scripts are loaded in the footer (in_footer = true) for better page render performance.
     *
     * @param string      $name    Asset base name (e.g. 'script', 'uikit').
     * @param string|null $version Optional version for cache busting; defaults to plugin version.
     * @param array       $deps    Array of script handles this file depends on (e.g. ['jquery']).
     * @return void
     */
    public function script(string $name, ?string $version = null, array $deps = []): void
    {
        if ($version == null) {
            $version = $this->version;
        }

        if ($this->path($name, 'script') !== null) {
            wp_enqueue_script(
                $this->prefix . $name,    // Unique handle: e.g. wpsm-script
                $this->path($name, 'script'), // Public URL to the JS file
                $deps,                    // Dependencies loaded before this script
                $version,                 // Cache busting version string
                true                      // Load in footer, not in <head>
            );
        }
    }
}
