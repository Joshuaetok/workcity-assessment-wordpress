<?php
namespace WCP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Shortcode
 *
 * Registers and renders the [client_projects] shortcode.
 */
class Shortcode {

    /**
     * Initialize: register shortcode and enqueue public assets.
     */
    public static function init() {
        add_shortcode( 'client_projects', [ __CLASS__, 'render_shortcode' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    /**
     * Enqueue frontend CSS/JS when shortcode is present.
     */
    public static function enqueue_assets() {
        global $post;
        if ( empty( $post ) || ! has_shortcode( $post->post_content, 'client_projects' ) ) {
            return;
        }

        wp_enqueue_style( 'wcp-public', WCP_URL . 'assets/public.css', [], '1.0' );
        wp_enqueue_script( 'wcp-public', WCP_URL . 'assets/public.js', [ 'jquery' ], '1.0', true );

        // Prepare project data for JS
        $data = self::get_projects_data();
        wp_localize_script( 'wcp-public', 'wcpProjectsData', $data );
    }

    /**
     * Shortcode callback: outputs container HTML.
     */
    public static function render_shortcode( $atts ) {
        $atts = shortcode_atts([
            'status' => 'all',
            'search' => 'true',
        ], $atts, 'client_projects' );

        ob_start();
        ?>
        <div class="projects-container">
            <h1><?php esc_html_e( 'Client Projects', 'workcity-client-projects' ); ?></h1>

            <div class="filters-section">
                <?php if ( 'true' === $atts['search'] ) : ?>
                <div class="filter-group">
                    <label for="wcp-search-input"><?php esc_html_e( 'Search by Title', 'workcity-client-projects' ); ?></label>
                    <input type="text" id="wcp-search-input" placeholder="<?php esc_attr_e( 'e.g., Website Redesign', 'workcity-client-projects' ); ?>">
                </div>
                <?php endif; ?>
                <div class="filter-group">
                    <label for="wcp-status-filter"><?php esc_html_e( 'Filter by Status', 'workcity-client-projects' ); ?></label>
                    <select id="wcp-status-filter">
                        <?php
                        $statuses = [
                            'all'       => __( 'All', 'workcity-client-projects' ),
                            'pending'   => __( 'Pending', 'workcity-client-projects' ),
                            'ongoing'   => __( 'In Progress', 'workcity-client-projects' ),
                            'completed' => __( 'Completed', 'workcity-client-projects' ),
                        ];
                        foreach ( $statuses as $value => $label ) {
                            printf(
                                '<option value="%1$s" %2$s>%3$s</option>',
                                esc_attr( $value ),
                                selected( $atts['status'], $value, false ),
                                esc_html( $label )
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="project-list" id="wcp-project-list"></div>
            <div id="wcp-no-results-message" class="no-results" style="display:none;"></div>
            <div class="pagination" id="wcp-pagination"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Fetch and format project data for JS.
     */
    private static function get_projects_data() {
        $query = new \WP_Query([
            'post_type'      => 'client_project',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        $projects = [];
        while ( $query->have_posts() ) {
            $query->the_post();
            $status   = get_post_meta( get_the_ID(), '_wcp_project_status', true ) ?: 'pending';
            $deadline = get_post_meta( get_the_ID(), '_wcp_project_deadline', true );
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
        return $projects;
    }
}

Shortcode::init();
