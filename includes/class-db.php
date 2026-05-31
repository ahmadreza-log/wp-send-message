<?php

/**
 * Database abstraction layer for WP Send Message plugin tables.
 *
 * Handles creation and removal of custom MySQL tables using WordPress dbDelta().
 * Tracks created table names in the wp_options table under 'wpsm_db_tables'.
 */

namespace WpSendMessage\Includes;

// Block direct file access outside WordPress context.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class DB
 *
 * Provides create() and drop() methods for plugin-specific database tables.
 * Table names are prefixed with {wpdb->prefix}send_message_ to avoid collisions.
 */
class DB
{
    /**
     * Suffix prepended after the WordPress table prefix for all plugin tables.
     * Example full name: wp_send_message_contacts
     *
     * @var string
     */
    private string $prefix = 'send_message_';

    /**
     * List of fully-qualified table names created by this plugin.
     * Loaded from wp_options on construction; updated after create/drop operations.
     *
     * @var array|null Null if the option has never been set.
     */
    private ?array $tables = null;

    /**
     * Loads the list of plugin tables from the WordPress options API.
     *
     * @return void
     */
    public function __construct()
    {
        // Retrieve persisted table registry; returns false if option does not exist.
        $this->tables = get_option('wpsm_db_tables');
    }

    /**
     * Creates a new database table if it does not already exist.
     *
     * Builds a CREATE TABLE SQL statement and executes it via dbDelta(),
     * which is WordPress-safe for schema migrations (adds missing columns, etc.).
     *
     * @param string $table   Short table name without prefix (e.g. 'contacts').
     * @param array  $columns Associative array of column_name => SQL type definition.
     * @return void
     * @throws \Exception If the global $wpdb object is unavailable.
     */
    public function create(string $table, array $columns): void
    {
        // Access WordPress database abstraction layer.
        global $wpdb;

        // Fail fast if database layer is not initialized (should never happen in WP).
        if (!$wpdb) {
            throw new \Exception('WPDB is not loaded');
        }

        // Prepend WordPress site prefix + plugin prefix unless already fully qualified.
        if (!str_contains($table, $wpdb->prefix)) {
            $table = $wpdb->prefix . $this->prefix . $table;
        }

        // Get charset/collation (e.g. utf8mb4_unicode_ci) for proper Unicode support.
        $charset = $wpdb->get_charset_collate();

        // Start building CREATE TABLE statement; ID column is always auto-increment primary key.
        $sql = "CREATE TABLE IF NOT EXISTS {$table} (";
        $sql .= "ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,";

        // Append each user-defined column from the $columns array.
        foreach ($columns as $key => $value) {
            $sql .= "{$key} {$value},";
        }

        // Close column definitions and set primary key constraint.
        $sql .= "PRIMARY KEY (ID)";
        $sql .= ") {$charset};";

        // dbDelta lives in wp-admin; load it if not already available (e.g. during activation).
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // Execute schema creation/migration safely through WordPress upgrade API.
        dbDelta($sql);

        // Register the new table name in the plugin's table registry option.
        // NOTE: If $this->tables is null, this may trigger a PHP warning — initialize first.
        $this->tables[] = $table;
        update_option('wpsm_db_tables', $this->tables);
    }

    /**
     * Permanently removes a plugin table from the database.
     *
     * Executes DROP TABLE IF EXISTS and removes the table name from wpsm_db_tables option.
     *
     * @param string $table Short table name without prefix (e.g. 'contacts').
     * @return void
     * @throws \Exception If the global $wpdb object is unavailable.
     */
    public function drop(string $table): void
    {
        global $wpdb;

        if (!$wpdb) {
            throw new \Exception('WPDB is not loaded');
        }

        // Resolve full table name with WordPress and plugin prefixes.
        if (!str_contains($table, $wpdb->prefix)) {
            $table = $wpdb->prefix . $this->prefix . $table;
        }

        // Execute raw DROP query; IF EXISTS prevents errors when table is already gone.
        $wpdb->query("DROP TABLE IF EXISTS {$table}");

        // Remove dropped table from the tracked list and persist the updated registry.
        $this->tables = array_diff($this->tables, [$table]);

        update_option('wpsm_db_tables', $this->tables);
    }
}
