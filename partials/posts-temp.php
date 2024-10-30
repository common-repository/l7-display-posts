<?php
use L7w\Primary_Tag_Plugin\Functions;

/**
 * Template partial displays the posts. 
 * 
 * This is included in the show_tags() function which is called by the
 * Shortcode [Display Posts].
 */
?>
<section itemscope itemtype='https://schema.org/BlogPosting' class="ptp-inner-cont <?php echo ( '2' == $two_columns ) ? 'two-columns' : ''; ?>">
	<div itemprop="author" itemscope itemtype="http://schema.org/Person" class="ptp-author-info">
		<div class="ptp-avatar">
			<?php if ( function_exists( 'get_avatar' ) ) { echo get_avatar( get_the_author_meta( 'email' ), 40 ); } ?>
		</div>
		<div class="ptp-author-desc">
			<h3>By: <span itemprop="name"><?php the_author_link(); ?></span></h3>
			<p><?php the_author_meta( 'description' ); ?></p>
		</div>
	</div>
	<h1 itemprop="headline" class="ptp-title"><?php echo get_the_title(); ?></h1>
	<div class="ptp-info">
		<span itemprop="datePublished" class="ptp-info-date"><?php the_date(); ?></span>
		<span class="ptp-info-category">Categories: <?php the_category( ', ' ); ?></span>
	</div>
	<div itemprop="articleBody" class="ptp-content">
		<?php the_excerpt(); ?>
	</div>
	<div class="ptp-info-tags">
		<p>Tags: <?php echo Functions\list_tags( get_the_tags() ); ?></p>
	</div>
</section>