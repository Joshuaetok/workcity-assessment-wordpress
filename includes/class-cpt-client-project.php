<?php
namespace WCP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CPT_Client_Project
 *
 * Registers the client_project custom post type and its meta boxes.
 */
class CPT_Client_Project {

    /**
     * Initialize hooks.
     */
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ] );
        add_action( 'save_post_client_project', [ __CLASS__, 'save_meta' ], 10, 2 );
    }

    /**
     * Register Custom Post Type: client_project.
     */
    public static function register_post_type() {
        $labels = [
            'name'                  => __( 'Client Projects', 'workcity-client-projects' ),
            'singular_name'         => __( 'Client Project', 'workcity-client-projects' ),
            'menu_name'             => __( 'Client Projects', 'workcity-client-projects' ),
            'name_admin_bar'        => __( 'Client Project', 'workcity-client-projects' ),
            'add_new'               => __( 'Add New', 'workcity-client-projects' ),
            'add_new_item'          => __( 'Add New Project', 'workcity-client-projects' ),
            'edit_item'             => __( 'Edit Project', 'workcity-client-projects' ),
            'new_item'              => __( 'New Project', 'workcity-client-projects' ),
            'view_item'             => __( 'View Project', 'workcity-client-projects' ),
            'search_items'          => __( 'Search Projects', 'workcity-client-projects' ),
            'not_found'             => __( 'No projects found.', 'workcity-client-projects' ),
            'not_found_in_trash'    => __( 'No projects found in Trash.', 'workcity-client-projects' ),
        ];

   /*     $args = [
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => [ 'title', 'editor' ],
            'has_archive'        => false,
            'capability_type'    => [ 'client_project', 'client_projects' ],
            'map_meta_cap'       => true,
        ];

        register_post_type( 'client_project', $args ); */

        $args = [
            'labels'              => $labels,
            'public'              => true,  // ← so WP_Query can retrieve them
            'publicly_queryable'  => true,  // ← directly approachable via front end
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,  // ← if you want Gutenberg & REST support
            'has_archive'         => true,
            'rewrite'             => [ 'slug' => 'client-projects' ],
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => [ 'title', 'editor' ],
            'capability_type'     => [ 'client_project', 'client_projects' ],
            'map_meta_cap'        => true,
          ];
          register_post_type( 'client_project', $args );
          
    }

    /**
     * Add meta boxes for client project.
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'wcp_client_name',
            __( 'Client Name', 'workcity-client-projects' ),
            [ __CLASS__, 'render_client_name_box' ],
            'client_project',
            'side',
            'default'
        );

        add_meta_box(
            'wcp_project_status',
            __( 'Project Status', 'workcity-client-projects' ),
            [ __CLASS__, 'render_status_box' ],
            'client_project',
            'side',
            'default'
        );

        add_meta_box(
            'wcp_project_deadline',
            __( 'Deadline', 'workcity-client-projects' ),
            [ __CLASS__, 'render_deadline_box' ],
            'client_project',
            'side',
            'default'
        );
    }

    /**
     * Render Client Name meta box.
     */
    public static function render_client_name_box( $post ) {
        wp_nonce_field( 'wcp_save_meta', 'wcp_meta_nonce' );
        $value = get_post_meta( $post->ID, '_wcp_client_name', true );
        echo '<label for="wcp_client_name_field">' . esc_html__( 'Enter client name:', 'workcity-client-projects' ) . '</label>'; 
        echo '<input type="text" id="wcp_client_name_field" name="wcp_client_name_field" value="' . esc_attr( $value ) . '" style="width:100%;" />';
    }

    /**
     * Render Project Status meta box.
     */
    public static function render_status_box( $post ) {
        $value = get_post_meta( $post->ID, '_wcp_project_status', true );
        $statuses = [ 'ongoing' => __( 'Ongoing', 'workcity-client-projects' ), 'completed' => __( 'Completed', 'workcity-client-projects' ), 'pending' => __( 'Pending', 'workcity-client-projects' ) ];
        wp_nonce_field( 'wcp_save_meta', 'wcp_meta_nonce' );
        echo '<select name="wcp_project_status_field" style="width:100%;">';
        foreach ( $statuses as $key => $label ) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr( $key ),
                selected( $value, $key, false ),
                esc_html( $label )
            );
        }
        echo '</select>';
    }

    /**
     * Render Deadline meta box.
     */
    public static function render_deadline_box( $post ) {
        wp_nonce_field( 'wcp_save_meta', 'wcp_meta_nonce' );
        $value = get_post_meta( $post->ID, '_wcp_project_deadline', true );
        echo '<label for="wcp_project_deadline_field">' . esc_html__( 'Select deadline:', 'workcity-client-projects' ) . '</label>'; 
        echo '<input type="date" id="wcp_project_deadline_field" name="wcp_project_deadline_field" value="' . esc_attr( $value ) . '" style="width:100%;" />';
    }

    /**
     * Save meta box data.
     */
    public static function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST['wcp_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wcp_meta_nonce'], 'wcp_save_meta' ) ) {
            return;
        }

        // Client Name
        if ( isset( $_POST['wcp_client_name_field'] ) ) {
            update_post_meta( $post_id, '_wcp_client_name', sanitize_text_field( $_POST['wcp_client_name_field'] ) );
        }

        // Project Status
        if ( isset( $_POST['wcp_project_status_field'] ) ) {
            update_post_meta( $post_id, '_wcp_project_status', sanitize_key( $_POST['wcp_project_status_field'] ) );
        }

        // Deadline
        if ( isset( $_POST['wcp_project_deadline_field'] ) ) {
            update_post_meta( $post_id, '_wcp_project_deadline', sanitize_text_field( $_POST['wcp_project_deadline_field'] ) );
        }
    }
}