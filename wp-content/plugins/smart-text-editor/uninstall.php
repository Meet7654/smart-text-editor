<?php
/**
 * Uninstall handler for Smart Text Editor.
 *
 * WordPress loads this file directly when the plugin is deleted from the
 * Plugins screen. The full plugin bootstrap is NOT loaded, which means only
 * this file runs — keeping the uninstall footprint minimal and safe.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

class STE_Uninstall {

    public static function run() {
        global $wpdb;

        // Drop the orders table
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ste_orders" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

        // Delete all plugin options
        $options = array(
            'ste_active_plan',
            'ste_license_key',
            'ste_license_activated',
            'ste_license_expires',
            'ste_billing_cycle',
            'ste_trial_used',
            'ste_trial_started',
            'ste_trial_expires',
            'ste_cf_mode',
            'ste_cf_app_id',
            'ste_cf_secret_key',
            'ste_db_version',
            'ste_flush_rewrite',
            'ste_license_salt',
        );

        foreach ( $options as $option ) {
            delete_option( $option );
        }
    }
}

STE_Uninstall::run();
