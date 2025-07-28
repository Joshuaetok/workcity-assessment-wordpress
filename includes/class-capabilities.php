<?php
namespace WCP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Capabilities
 *
 * Adds and removes custom capabilities on activation/deactivation.
 */
class Capabilities {

    /** List of capabilities to add. */
    private static $caps = [
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

    /** Add caps to administrator on plugin activation. */
    public static function add_caps() {
        $role = get_role( 'administrator' );
        if ( ! $role ) {
            return;
        }
        foreach ( self::$caps as $cap ) {
            $role->add_cap( $cap );
        }
    }

    /** Remove caps on plugin deactivation. */
    public static function remove_caps() {
        $role = get_role( 'administrator' );
        if ( ! $role ) {
            return;
        }
        foreach ( self::$caps as $cap ) {
            $role->remove_cap( $cap );
        }
    }
}
