<?php
namespace WCP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Admin_UI
 *
 * Handles admin list table customizations, filters, and enqueueing assets.
 */
class Admin_UI {

    /**
     * Initialize hooks.
     */
    public static function init() {
        // Enqueue admin assets
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );

        // Customize columns
        add_filter( 'manage_client_project_posts_columns', [ __CLASS__, 'set_custom_columns' ] );
        add_action( 'manage_client_project_posts_custom_column', [ __CLASS__, 'render_custom_columns' ], 10, 2 );

        // Make columns sortable
        add_filter( 'manage_edit-client_project_sortable_columns', [ __CLASS__, 'set_sortable_columns' ] );

        // Add status filter dropdown
        add_action( 'restrict_manage_posts', [ __CLASS__, 'add_status_filter' ] );
        add_filter( 'parse_query', [ __CLASS__, 'filter_by_status' ] );
    }

    /**
     * Enqueue admin CSS and JS only on our CPT screens.
     */
    public static function enqueue_assets( $hook ) {
        global $post_type;
        if ( in_array( $hook, [ 'edit.php', 'post.php', 'post-new.php' ], true ) && 'client_project' === $post_type ) {
            wp_enqueue_style( 'wcp-admin', WCP_URL . 'assets/admin.css', [], '1.0' );
            wp_enqueue_script( 'wcp-admin', WCP_URL . 'assets/admin.js', [], '1.0', true );
        }
    }

    /**
     * Define custom columns for Client Projects list table.
     */
    public static function set_custom_columns( $columns ) {
        $new = [
            'cb'           => $columns['cb'],
            'title'        => __( 'Project Title', 'workcity-client-projects' ),
            'client_name'  => __( 'Client Name', 'workcity-client-projects' ),
            'status'       => __( 'Status', 'workcity-client-projects' ),
            'deadline'     => __( 'Deadline', 'workcity-client-projects' ),
            'date'         => __( 'Date Created', 'workcity-client-projects' ),
        ];
        return $new;
    }

    /**
     * Populate custom column values.
     */
    public static function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'client_name':
                echo esc_html( get_post_meta( $post_id, '_wcp_client_name', true ) );
                break;

            case 'status':
                $status = get_post_meta( $post_id, '_wcp_project_status', true );
                $labels = [
                    'ongoing'   => __( 'Ongoing', 'workcity-client-projects' ),
                    'completed' => __( 'Completed', 'workcity-client-projects' ),
                    'pending'   => __( 'Pending', 'workcity-client-projects' ),
                ];
                $class = 'status-' . esc_attr( $status );
                printf(
                    '<span class="status-badge %1$s">%2$s</span>',
                    $class,
                    isset( $labels[ $status ] ) ? esc_html( $labels[ $status ] ) : ''
                );
                break;

            case 'deadline':
                $date = get_post_meta( $post_id, '_wcp_project_deadline', true );
                echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '-';
                break;
        }
    }

    /**
     * Register sortable columns.
     */
    public static function set_sortable_columns( $columns ) {
        $columns['client_name'] = 'client_name';
        $columns['status']      = 'status';
        $columns['deadline']    = 'deadline';
        return $columns;
    }

    /**
     * Add status filter dropdown above list table.
     */
    public static function add_status_filter() {
        global $typenow;
        if ( 'client_project' !== $typenow ) return;

        $current = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
        $options = [ '' => __( 'All Statuses', 'workcity-client-projects' ), 'ongoing' => __( 'Ongoing', 'workcity-client-projects' ), 'completed' => __( 'Completed', 'workcity-client-projects' ), 'pending' => __( 'Pending', 'workcity-client-projects' ) ];

        echo '<select name="status" id="status-filter"><option value="">'. esc_html( $options[''] ) .'</option>';
        foreach ( $options as $value => $label ) {
            if ( '' === $value ) continue;
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr( $value ),
                selected( $current, $value, false ),
                esc_html( $label )
            );
        }
        echo '</select>';
    }

    /**
     * Filter the admin list query by selected status.
     */
    public static function filter_by_status( $query ) {
        global $pagenow;
        $type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

        if ( 'edit.php' === $pagenow && 'client_project' === $type && ! empty( $_GET['status'] ) ) {
            $query->query_vars['meta_key']   = '_wcp_project_status';
            $query->query_vars['meta_value'] = sanitize_key( $_GET['status'] );
        }
    }
}
