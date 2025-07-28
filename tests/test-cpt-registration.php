<?php
/**
 * Tests for Custom Post Type registration.
 *
 * @package workcity-client-projects
 */

class Test_CPT_Registration extends WP_UnitTestCase {

    public function test_cpt_client_project_exists() {
        $this->assertTrue( post_type_exists( 'client_project' ), 'client_project CPT should be registered.' );
    }

    public function test_cpt_has_correct_labels() {
        $pt = get_post_type_object( 'client_project' );
        $this->assertNotNull( $pt );
        $this->assertEquals( 'Client Projects', $pt->labels->name );
        $this->assertEquals( 'Client Project',  $pt->labels->singular_name );
    }
}
