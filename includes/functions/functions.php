<?php
namespace L7w\Primary_Tag_Plugin\Functions;

use Primary_Tag_Plugin\Php\Shortcode as Shortcode;

/**
 * Create a slug out of a tag name.
 * 
 * The WP-query attribute for tag requires a slug. 
 * The user will most likely give us the
 * name of the tag. Makes the spaces a '-' and changes all
 * characters to lowercase.
 * 
 * @param  string		$title 		Tag name. May have spaces and capital letters.
 * @return string        			Spaces replaced with '-' all lowercase
 */
function create_slug( $title ){
	$slug = trim( strtolower( $title ) );
	return str_replace( ' ', '-', $slug );
}

/**
 * Add the "read more" link to the posts.
 *
 * @uses  get_permalink()
 * @uses  get_the_id()
 * 
 * @param  string 	$more 		The current 'more' text.
 * @return string       		The "read more" html markup.
 */
function exert_read_more( $more ) {
	return ' <a class="read-more" href="' . get_permalink( get_the_ID() ) . '">' . __( 'Read More', 'ptp' ) . '</a>';
}

/**
 * List the tags of posts as links. 
 * 
 * Creates links to tag posts in a comma separated list. 
 * 
 * @param  array 	$posttags  	The array of tags
 * 
 * @return string           	String of tags.
 * @return false 				Returns false if array not given or set.
 */
function list_tags( $posttags ){
	if ( isset( $posttags ) && is_array( $posttags ) ){
		ob_start();
		foreach ( $posttags as $tag ) {
			if ( $tag === end( $posttags ) ) {
				?><a href="<?php echo esc_attr( get_tag_link(  $tag->term_id ) ); ?>"> <?php echo esc_html( $tag->name ); ?></a><?php
			}
			else {
				?><a href="<?php echo esc_attr( get_tag_link( $tag->term_id ) ); ?>"> <?php echo esc_html( $tag->name ); ?></a>, <?php
			}
		}
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
	else {
		return false;
	}
}

/**
 * Get the shortcode atts from content
 *
 * Gets the shortcode attributes from content and
 * puts them into an array.
 * 
 * @param  string $content Page content.
 * @return array           Array of attributes.
 */
function get_shortcode_atts( $content ){
	if ( ! empty( $content ) ){
		$atts_transient = array();
		preg_match( '/\[Display Posts (.*?)\]/', $content, $atts );

		if ( isset( $atts[1] ) && ! empty( $atts[1] ) ){
			$options = explode( ' ', trim( $atts[1] ) );
			foreach ( $options as $option ) {
				$temp = explode( '=', stripslashes( str_replace( array( '"', '\'' ), '', $option ) ) );
				$atts_transient[$temp[0]] = $temp[1];
			}
		}
		return $atts_transient;
	}
	return $content;
}

/**
 * Set a transient with shortcode attributes.
 *
 * Check for shortcode on page save. If the shortcode 
 * exists extract the parameters and save them into the ptp_query_atts_options transient.
 * This will then be pulled when a post is saved to prime the cache
 * with the correct parameters. This is a resource intensive function but
 * it is happening on the admin side and the hard work is only happening if 
 * the shortcode is pressent. 
 *
 * @uses  has_shortcode()
 * @uses  set_transient()
 * @uses  prime_cache_display_posts()
 * 
 * @param  string $content The content of the page before it enters the database.
 * @return string          The same page content.
 */
function check_for_shortcode( $content ){
	global $post;
	$post_slug = $post->post_name;

	if ( has_shortcode( $content, 'Display Posts' ) ){
		$atts_transient = get_shortcode_atts( $content );
		
		// Set the transient and prime the cache.
		if ( ! set_transient( $post_slug . '_ptp_query_atts_options', $atts_transient, 0 ) ){
			error_log( 'Error: Transient was not set.' );
		}

		// Prime the cache.
		if ( false == prime_cache_display_posts( $post_slug, $atts_transient ) ){
			error_log( 'Error: Prime cache failed.' );
		}
	}
	return $content;
}

/**
 * Prime the object cache. 
 * 
 * Gets the atts transient set by check_for_shortcode.
 * Passes the atts to the show_tags function with the prime parameter set 
 * to true. This will prime the object cache with the correct query.
 *
 * @uses  show_tags()
 *
 * @param string 	$page 	Page slug identifying what page these option are for.
 * @param array 	$atts 	Array of options to prime the cache with.
 * 
 * @return boolean	False if get transient fails, otherwise true.
 */
function prime_cache_display_posts( $page = '', $atts = '' ) {
	global $post;

	if ( '' == $page ){
		$page = $post->post_name;
	}
	
	// Check if an array is being provided.
	// If it isn't than we get it from the transients.
	if ( ! is_array( $atts ) ){
		$atts = get_transient( $page . '_ptp_query_atts_options' );
	}

	error_log( print_r( $atts, true ) );
	if ( is_array( $atts ) ) {
		$atts['prime'] = true;
		error_log( print_r( $atts, true ) );
		Shortcode\show_tags( $atts );
		return true;
	}
	else {
		return false;
	}
}