<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
get_header('shop'); ?>
<div id="page">
	<div class="single-content-wrapper <?php if ( mts_custom_sidebar() == 'mts_nosidebar' ) { echo 'no-sidebar'; }?>">		
		<article class="<?php mts_article_class(); ?>">
			<div id="content_box" >
				<?php do_action('woocommerce_before_main_content'); ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php wc_get_template_part( 'content', 'single-product' ); ?>
					<?php endwhile; // end of the loop. ?>
				<?php do_action('woocommerce_after_main_content'); ?>
			</div>
		</article>
		<?php /*do_action('woocommerce_sidebar');*/ ?>
		<?php get_sidebar(); ?>
	</div>	
<?php get_footer(); ?>
