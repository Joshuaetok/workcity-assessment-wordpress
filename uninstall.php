<?php
/**
 * Fired when the plugin is uninstalled.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove custom capabilities from the administrator role.
$role = get_role( 'administrator' );
if ( $role ) {
    $caps = [
        'edit_client_project',
        'read_client_project',
        'delete_client_project',
        'edit_client_projects',
        'edit_others_client_projects',
        'publish_client_projects',
        'read_private_client_projects',
        'delete_client_projects',
        'delete_private_client_projects',
        'delete_published_client_projects',
        'delete_others_client_projects',
        'edit_private_client_projects',
        'edit_published_client_projects',
    ];
    foreach ( $caps as $cap ) {
        $role->remove_cap( $cap );
    }
}

// (Optional) Remove any plugin options here, e.g.:
// delete_option( 'wcp_some_setting' );
