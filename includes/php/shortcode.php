<?php
namespace Primary_Tag_Plugin\php\shortcode;

/**
 * Display Posts shortcode callback.
 * 
 * Queries and displays the posts with provided
 * tag attribute, number of posts, order of post, orderby, use pages, 1 or two columns. 
 * Caches query object by using set_transient(). When utilizing memcache the transients will
 * be stored in memory making them 
 * If no attributes are supplied it will return all posts.
 *
 * @uses  create_slug() Creates a slug from the tag title.
 * 
 * @param  array $atts Contains the attributes from the shortcode
 * @return string        Returns the html to display the posts.
 */

use L7w\Primary_Tag_Plugin\Functions as Functions;

function show_tags( $atts ) {
	
	/**
	 * Get page slug. Use this to add to the
	 * set transient function.  This will make 
	 * the shortcode work with object caching on
	 * multiple pages.
	 */
	global $post;
	$post_slug = $post->post_name;

	/**
	 * Using shortcode_atts we set the default attributes if they
	 * have not set in the shortcode.
	 */
	$atts = shortcode_atts( array(
		'tag' 			=> '',
		'cat'			=> '',
		'posts' 		=> '500',
		'order'			=> 'DESC',
		'orderby'		=> 'date',
		'pages' 		=> 'false',
		'columns'		=> '1',
		'prime'			=> false,
	), $atts );

	// Make tag att a slug if it isn't already.
	$tag = Functions\create_slug( esc_html( $atts['tag'] ) );

	// Make cat att a slug if it isn't already.
	$cat = Functions\create_slug( esc_html( $atts['cat'] ) );

	// Set how many posts variable
	$num_posts = esc_html( $atts['posts'] );

	// How shall we order them? ASC or DESC
	$ord_posts = strtoupper( esc_html( $atts['order'] ) );

	// What should we order them by? title, date, etc.
	$ordby_posts = esc_html( $atts['orderby'] );

	/**
	 * Make the query as efficient as possible. If the
	 * option to not have pages is set to false.  We want the no_found_rows
	 * attribute in our query to be set to true.  If 'pages' is set to false
	 * we set the no_found_rows to true.
	 */
	$no_found_rows = esc_html( $atts['pages'] );
	$no_found_rows = $no_found_rows === 'true'? false: true;

	// The results in one column or two.
	$two_columns = esc_html( absint( $atts['columns'] ) );

	/**
	 * Get the page we are on for pagination. If pages is set to
	 * true then we need to know what page we are on for pagination.
	 * So we set that variable here and pass it into the query. This is
	 * also used in the wp_cache_get and wp_cache_get for caching the
	 * different page queries.
	 */
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

	/**
	 * Get the cached query if there is one. 
	 * But not if we are priming the cache.
	 */
	if ( false == $atts['prime'] ){

		/**
		 * When object caching is enabled (memcache) set_transient will stop using the default database layer(slow).
		 * Transient values are then stored in fast memory instead of in the database.  They become a wrapper for
		 * wp_cache_get and set.
		 */
		$query_results = get_transient( $paged . '_' . $post_slug . '_ptp_cached_post' );
		error_log( $paged . '_' . $post_slug . '_ptp_cached_post' );

		if ( false == $query_results){
			error_log('No stored transient.');
		}
		else {
			error_log('Got stored transient.');
		}
	}
	else {
		$query_results = false;
	}

	/**
	 * If there isn't a cached query. We build the query arguments and
	 * execute the query.  If the result is error free and has posts then
	 * we cache it.
	 */
	if ( false == $query_results ){
		$args = array(
			'post_type' 		=> 'post',
			'post_status'     	=> 'publish',
			'category_name'		=> $cat,
			'tag'				=> $tag,
			'posts_per_page' 	=> $num_posts,
			'order'				=> $ord_posts,
			'orderby'			=> $ordby_posts,
			'paged'				=> $paged,
			'no_found_rows' 	=> $no_found_rows,
		);
		$query_results = new \WP_Query( $args );

		/**
		 * Cache the result adding the page number to the key. 
		 * If there are more than one page cache those pages too.
		 */
		if ( ! is_wp_error( $query_results ) && $query_results->have_posts() ) {
			$total_pages = $query_results->max_num_pages;

			/**
			 * When there is no attributes provided max_pages returns 0
			 * We make 0 a 1 for proper transient key name creation.
			 */
			$total_pages = ( 0 === $total_pages) ? 1 : $total_pages;

			// Cache all the pages for this query.
			for ( $x = 1; $x <= $total_pages; $x++ ){
				error_log( 'setting transient' );
				$set = set_transient( $x . '_' . $post_slug . '_ptp_cached_post', $query_results, 0 );
				if (false != $set ){ error_log( 'set transient: ' . $x . '_' . $post_slug . '_ptp_cached_post' ); }
			}
		}
	}

	/**
	 * Loop out the posts. Includes the post-temp.php file for display.
	 * Only loop out the post if 'prime' option is set to false.
	 */
	if ( false == $atts['prime'] ){

		// Enqueue the style sheet.
		wp_enqueue_style( 'ptp-styles' );

		// Begin the loop
		ob_start();
		if ( $query_results->have_posts() ) :
			?>
			<div class="ptp-container">
				<?php
				while ( $query_results->have_posts() ) : $query_results->the_post();
					include( PTP_DIR . 'partials/posts-temp.php' );
				endwhile;
				?>
				<div>
					<?php
					next_posts_link( '<button>' . esc_html__( 'Older', 'ptp' ) . '</button>', $query_results->max_num_pages );
					previous_posts_link( '<button>' . esc_html__( 'Newer', 'ptp' ) . '</button>', $query_results->max_num_pages );
					?>
				</div>
			</div>	
			<?php 
		endif;
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'ptp-filter-posts', $content );
	}
}