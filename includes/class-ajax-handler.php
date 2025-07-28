<?php
namespace WCP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Ajax_Handler
 *
 * Handles AJAX filtering for the [client_projects] shortcode.
 */
class Ajax_Handler {

    public static function init() {
        // Front‑end AJAX (logged‑in & logged‑out)
        add_action( 'wp_ajax_wcp_filter_projects',   [ __CLASS__, 'filter_projects' ] );
        add_action( 'wp_ajax_nopriv_wcp_filter_projects', [ __CLASS__, 'filter_projects' ] );
    }

    /**
     * Query projects based on $_POST filters and return JSON.
     */
    public static function filter_projects() {
        // Verify nonce if you’ve localized one (optional)
        // if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wcp-ajax-nonce' ) ) {
        //     wp_send_json_error( 'Invalid nonce' );
        // }

        $status = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'all';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

        $args = [
            'post_type'      => 'client_project',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        // Status filter
        if ( $status !== 'all' ) {
            $args['meta_query'] = [
                [
                    'key'   => '_wcp_project_status',
                    'value' => $status,
                ],
            ];
        }

        // Search filter
        if ( $search ) {
            $args['s'] = $search;
        }

        $query = new \WP_Query( $args );
        $projects = [];

        while ( $query->have_posts() ) {
            $query->the_post();
            $status    = get_post_meta( get_the_ID(), '_wcp_project_status', true ) ?: 'pending';
            $deadline  = get_post_meta( get_the_ID(), '_wcp_project_deadline', true );
            $projects[] = [
                'title'       => get_the_title(),
                'status'      => $status,
                'status_label'=> ucfirst( $status ),
                'startDate'   => get_the_date( '', get_the_ID() ),
                'endDate'     => $deadline ? date_i18n( get_option('date_format'), strtotime($deadline) ) : __( 'N/A', 'workcity-client-projects' ),
                'description' => get_the_excerpt(),
            ];
        }
        wp_reset_postdata();

        wp_send_json_success( $projects );
    }
}

// Initialize AJAX handlers
Ajax_Handler::init();
