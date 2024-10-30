<?php
namespace L7w\Primary_Tag_Plugin\Core;

/**
 * Contains setup function and initialization functions.
 */

/**
 * Default setup routine
 *
 * @uses add_action()
 * @uses do_action()
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );

	do_action( 'ptp_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @uses apply_filters()
 * @uses get_locale()
 * @uses load_textdomain()
 * @uses load_plugin_textdomain()
 * @uses plugin_basename()
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'ptp' );
	load_textdomain( 'ptp', WP_LANG_DIR . '/ptp/ptp-' . $locale . '.mo' );
	load_plugin_textdomain( 'ptp', false, plugin_basename( PTP_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * 
 * Add the "Display Posts" shortcode.
 * Registers the primary-tag-plugin.min.css style sheet.
 * Adds filter 'exerpt_more' for a simple read more link on posts.
 * Adds action on save post to prime the cache with correct parameters.
 * Adds action to prime cache everytime a post is updated or created.
 * 
 * @uses do_action()
 * @uses add_shortcode()
 * @uses wp_register_style()
 * @uses wp_enqueue_style()
 * @uses add_filter()
 * @uses add_action()
 *
 * @return void
 */
function init() {
	do_action( 'ptp_init' );

	// The Primary Tag shorcode.
	add_shortcode( 'Display Posts', 'Primary_Tag_Plugin\php\shortcode\show_tags' );

	// Register the style sheet.
	wp_register_style( 'ptp-styles', plugins_url( '../../assets/css/primary-tag-plugin.min.css', __FILE__ ) );

	// Filter for "read more".
	add_filter( 'excerpt_more', 'L7w\Primary_Tag_Plugin\Functions\exert_read_more' );

	/**
	 * Add filter to find the shotcode and pull the parameters so we can update
	 * The object cache with the correct query.
	 */
	add_filter( 'content_save_pre', 'L7w\Primary_Tag_Plugin\Functions\check_for_shortcode', 10, 1 );

	/**
	 * Everytime a post is updated or saved we are going to prime the cache with
	 * the query contained in our shortcode. Not planing on there being more than one shortcode
	 * on the whole site just yet. We can add a page slug to the cache key for that.
	 */
	add_action( 'save_post_post', 'L7w\Primary_Tag_Plugin\Functions\prime_cache_display_posts', 10, 1 );
}

/**
 * Activate the plugin
 *
 * @uses init()
 * @uses flush_rewrite_rules()
 *
 * @return void
 */
function activate() {

	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * @return void
 */
function deactivate() {
	// Nothing
}