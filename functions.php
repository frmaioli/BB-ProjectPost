<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'BB_PROJECTPOST_admin_enqueue_script' ) ) {
	function BB_PROJECTPOST_admin_enqueue_script() {
		wp_enqueue_style( 'buddyboss-addon-admin-css', plugin_dir_url( __FILE__ ) . 'style.css' );
	}

	add_action( 'admin_enqueue_scripts', 'BB_PROJECTPOST_admin_enqueue_script' );
}

if ( ! function_exists( 'BB_PROJECTPOST_get_settings_sections' ) ) {
	function BB_PROJECTPOST_get_settings_sections() {

		$settings = array(
			'BB_PROJECTPOST_settings_section' => array(
				'page'  => 'addon',
				'title' => __( 'ProjectPost Settings', 'buddyboss-platform-addon' ),
			),
		);

		return (array) apply_filters( 'BB_PROJECTPOST_get_settings_sections', $settings );
	}
}

if ( ! function_exists( 'BB_PROJECTPOST_get_settings_fields_for_section' ) ) {
	function BB_PROJECTPOST_get_settings_fields_for_section( $section_id = '' ) {

		// Bail if section is empty
		if ( empty( $section_id ) ) {
			return false;
		}

		$fields = BB_PROJECTPOST_get_settings_fields();
		$retval = isset( $fields[ $section_id ] ) ? $fields[ $section_id ] : false;

		return (array) apply_filters( 'BB_PROJECTPOST_get_settings_fields_for_section', $retval, $section_id );
	}
}

if ( ! function_exists( 'BB_PROJECTPOST_get_settings_fields' ) ) {
	function BB_PROJECTPOST_get_settings_fields() {

		$fields = array();

		$fields['BB_PROJECTPOST_settings_section'] = array(

			'BB_PROJECTPOST_field' => array(
				'title'             => __( 'ProjectPost Field', 'buddyboss-platform-addon' ),
				'callback'          => 'BB_PROJECTPOST_settings_callback_field',
				'sanitize_callback' => 'absint',
				'args'              => array(),
			),

		);

		return (array) apply_filters( 'BB_PROJECTPOST_get_settings_fields', $fields );
	}
}

if ( ! function_exists( 'BB_PROJECTPOST_settings_callback_field' ) ) {
	function BB_PROJECTPOST_settings_callback_field() {
		?>
        <input name="BB_PROJECTPOST_field"
               id="BB_PROJECTPOST_field"
               type="checkbox"
               value="1"
			<?php checked( BB_PROJECTPOST_is_addon_field_enabled() ); ?>
        />
        <label for="BB_PROJECTPOST_field">
			<?php _e( 'Enable this option', 'buddyboss-platform-addon' ); ?>
        </label>
		<?php
	}
}

if ( ! function_exists( 'BB_PROJECTPOST_is_addon_field_enabled' ) ) {
	function BB_PROJECTPOST_is_addon_field_enabled( $default = 1 ) {
		return (bool) apply_filters( 'BB_PROJECTPOST_is_addon_field_enabled', (bool) get_option( 'BB_PROJECTPOST_field', $default ) );
	}
}

/***************************** Add section in current settings ***************************************/

/**
 * Register fields for settings hooks
 * bp_admin_setting_general_register_fields
 * bp_admin_setting_xprofile_register_fields
 * bp_admin_setting_groups_register_fields
 * bp_admin_setting_forums_register_fields
 * bp_admin_setting_activity_register_fields
 * bp_admin_setting_media_register_fields
 * bp_admin_setting_friends_register_fields
 * bp_admin_setting_invites_register_fields
 * bp_admin_setting_search_register_fields
 */
if ( ! function_exists( 'BB_PROJECTPOST_bp_admin_setting_general_register_fields' ) ) {
    function BB_PROJECTPOST_bp_admin_setting_general_register_fields( $setting ) {
	    // Main General Settings Section
	    $setting->add_section( 'BB_PROJECTPOST_addon', __( 'Add-on Settings', 'buddyboss-platform-addon' ) );

	    $args          = array();
	    $setting->add_field( 'bp-enable-my-addon', __( 'My Field', 'buddyboss-platform-addon' ), 'BB_PROJECTPOST_admin_general_setting_callback_my_addon', 'intval', $args );
    }

	add_action( 'bp_admin_setting_general_register_fields', 'BB_PROJECTPOST_bp_admin_setting_general_register_fields' );
}

if ( ! function_exists( 'BB_PROJECTPOST_admin_general_setting_callback_my_addon' ) ) {
	function BB_PROJECTPOST_admin_general_setting_callback_my_addon() {
		?>
        <input id="bp-enable-my-addon" name="bp-enable-my-addon" type="checkbox"
               value="1" <?php checked( BB_PROJECTPOST_enable_my_addon() ); ?> />
        <label for="bp-enable-my-addon"><?php _e( 'Enable my option', 'buddyboss-platform-addon' ); ?></label>
		<?php
	}
}

if ( ! function_exists( 'BB_PROJECTPOST_enable_my_addon' ) ) {
	function BB_PROJECTPOST_enable_my_addon( $default = false ) {
		return (bool) apply_filters( 'BB_PROJECTPOST_enable_my_addon', (bool) bp_get_option( 'bp-enable-my-addon', $default ) );
	}
}


/**************************************** MY PLUGIN INTEGRATION ************************************/

/**
 * Set up the my plugin integration.
 */
function BB_PROJECTPOST_register_integration() {
	require_once dirname( __FILE__ ) . '/integration/buddyboss-integration.php';
	buddypress()->integrations['addon'] = new BB_PROJECTPOST_BuddyBoss_Integration();
}
add_action( 'bp_setup_integrations', 'BB_PROJECTPOST_register_integration' );


/**************************************** WP INTEGRATION ************************************/
// Hook ht_custom_post_custom_article() to the init action hook
add_action('init', 'create_project_post_type');
function create_project_post_type() {
    $labels = array(
        'name'               => __('Projects', 'text-domain'),
        'singular_name'      => __('Project', 'text-domain'),
        'menu_name'          => __('Projects', 'text-domain'),
        'name_admin_bar'     => __('Project', 'text-domain'),
        'add_new'            => __('Add New', 'text-domain'),
        'add_new_item'       => __('Add New Project', 'text-domain'),
        'new_item'           => __('New Project', 'text-domain'),
        'edit_item'          => __('Edit Project', 'text-domain'),
        'view_item'          => __('View Project', 'text-domain'),
        'all_items'          => __('All Projects', 'text-domain'),
        'search_items'       => __('Search Projects', 'text-domain'),
        'not_found'          => __('No Projects found.', 'text-domain'),
        'not_found_in_trash' => __('No Projects found in Trash.', 'text-domain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true, // Show in admin UI
        'show_in_menu'       => true, // Show in the menu
        'query_var'          => true,
        'rewrite'            => array('slug' => 'projects'), // Customize the slug
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'author', 'comments', 'custom-fields'), // Add additional features as needed
        'taxonomies'         => array('category', 'post_tag'), // Adding support for categories
    );

    register_post_type('project', $args);
}

// Register the custom post type by hooking into the 'init' action
add_action('init', 'create_project_post_type');