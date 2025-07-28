<?php
namespace WCP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class REST_API
 *
 * Registers a custom REST endpoint for client projects.
 */
class REST_API {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        register_rest_route( 'wcp/v1', '/projects', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_projects' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'status' => [
                    'validate_callback' => function( $param ) {
                        return in_array( $param, [ 'all', 'pending', 'ongoing', 'completed' ], true );
                    },
                    'default' => 'all',
                ],
                'search' => [
                    'sanitize_callback' => 'sanitize_text_field',
                    'default'           => '',
                ],
                'per_page' => [
                    'validate_callback' => 'is_numeric',
                    'default'           => 10,
                ],
                'page' => [
                    'validate_callback' => 'is_numeric',
                    'default'           => 1,
                ],
            ],
        ] );
    }

    public static function get_projects( \WP_REST_Request $request ) {
        $status   = $request->get_param( 'status' );
        $search   = $request->get_param( 'search' );
        $per_page = (int) $request->get_param( 'per_page' );
        $page     = (int) $request->get_param( 'page' );

        $args = [
            'post_type'      => 'client_project',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
        ];

        if ( $status !== 'all' ) {
            $args['meta_query'] = [
                [
                    'key'   => '_wcp_project_status',
                    'value' => $status,
                ],
            ];
        }

        if ( $search ) {
            $args['s'] = $search;
        }

        $query = new \WP_Query( $args );
        $data  = [];

        foreach ( $query->posts as $post ) {
            $status   = get_post_meta( $post->ID, '_wcp_project_status', true ) ?: 'pending';
            $deadline = get_post_meta( $post->ID, '_wcp_project_deadline', true );
            $data[] = [
                'id'          => $post->ID,
                'title'       => $post->post_title,
                'status'      => $status,
                'status_label'=> ucfirst( $status ),
                'startDate'   => date_i18n( get_option('date_format'), strtotime( $post->post_date ) ),
                'endDate'     => $deadline ? date_i18n( get_option('date_format'), strtotime($deadline) ) : __( 'N/A', 'workcity-client-projects' ),
                'excerpt'     => wp_strip_all_tags( $post->post_excerpt ?: wp_trim_words( $post->post_content, 20 ) ),
                'link'        => get_permalink( $post->ID ),
            ];
        }

        return rest_ensure_response( [
            'projects'      => $data,
            'total'         => (int) $query->found_posts,
            'per_page'      => $per_page,
            'current_page'  => $page,
            'total_pages'   => (int) $query->max_num_pages,
        ] );
    }
}

// Initialize REST API
REST_API::init();
