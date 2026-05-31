<?php
/**
 * Admin template: Contacts list and "Add Contact" modal form.
 *
 * Rendered by Admin::callback() when visiting admin.php?page=wpsm-contacts.
 * Uses UIkit components (modal, table, form icons) for the admin UI.
 *
 * This file is included inside the .wrap container; do not output full HTML document.
 */

// Access WordPress database for future contact queries (currently unused).
global $wpdb;

// TODO: Fetch contacts from {prefix}send_message_contacts and assign to $items.
// $items =
?>

<!-- Button opens the Add Contact modal via UIkit toggle attribute -->
<button uk-toggle="target: #add-contact-modal" type="button" class="page-title-action"><?php esc_html_e('Add Contact', 'wp-send-message'); ?></button>

<!-- UIkit modal container; hidden until triggered by the button above -->
<div id="add-contact-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">

        <!--
            AJAX form: data-ajax-form attribute is intercepted by assets/js/script.js.
            Submits to admin-ajax.php instead of a traditional page reload.
        -->
        <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" data-ajax-form>

            <!-- Tells WordPress which wp_ajax_* hook to invoke -->
            <input type="hidden" name="action" value="add-contact">

            <!-- CSRF protection; verified in Ajax::add_contact() with wp_verify_nonce() -->
            <?php wp_nonce_field('add-contact'); ?>

            <!-- Modal header with title -->
            <div class="uk-modal-header">
                <h2 class="uk-modal-title"><?php esc_html_e('Add Contact', 'wp-send-message'); ?></h2>
            </div>

            <!-- Modal body containing form fields -->
            <div class="uk-modal-body">

                <!-- Name field (required) -->
                <div class="uk-margin">
                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon" uk-icon="icon: user"></span>
                        <input class="uk-input" id="contact-name" name="name" type="text" placeholder="<?php esc_html_e('Name', 'wp-send-message'); ?>">
                    </div>
                </div>

                <!-- Email field (optional if phone is provided) -->
                <div class="uk-margin">
                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon" uk-icon="icon: mail"></span>
                        <input class="uk-input" id="contact-email" name="email" type="text" placeholder="<?php esc_html_e('Email', 'wp-send-message'); ?>">
                    </div>
                </div>

                <!-- Phone field (optional if email is provided) -->
                <div class="uk-margin">
                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon" uk-icon="icon: phone"></span>
                        <input class="uk-input" id="contact-phone" name="phone" type="text" placeholder="<?php esc_html_e('Phone', 'wp-send-message'); ?>">
                    </div>
                </div>

            </div>

            <!-- Modal footer with submit button -->
            <div class="uk-modal-footer">
                <input type="submit" name="submit" class="button button-primary" value="<?php esc_html_e('Save Contact', 'wp-send-message'); ?>">
            </div>

        </form>

    </div>
</div>

<!-- Contacts data table (placeholder rows until DB query is implemented) -->
<table class="uk-table uk-background-default uk-overflow-auto">
    <thead>
        <tr>
            <th class="uk-table-shrink"></th>
            <th class="uk-table-shrink"><?php esc_html_e('ID', 'wp-send-message'); ?></th>
            <th class="uk-table-expand"><?php esc_html_e('Phone', 'wp-send-message'); ?></th>
            <th class="uk-table-expand"><?php esc_html_e('Email', 'wp-send-message'); ?></th>
            <th><?php esc_html_e('Action', 'wp-send-message'); ?></th>
        </tr>
    </thead>
    <tbody>
        <!-- TODO: Loop over $items and output one <tr> per contact -->
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
    </tbody>
    <tfoot>
        <!-- Duplicate header row for accessibility / print styling -->
        <tr>
            <td class="uk-table-shrink"></td>
            <td class="uk-table-shrink"><?php esc_html_e('ID', 'wp-send-message'); ?></td>
            <td class="uk-table-expand"><?php esc_html_e('Phone', 'wp-send-message'); ?></td>
            <td class="uk-table-expand"><?php esc_html_e('Email', 'wp-send-message'); ?></td>
            <td><?php esc_html_e('Action', 'wp-send-message'); ?></td>
        </tr>
    </tfoot>
</table>
