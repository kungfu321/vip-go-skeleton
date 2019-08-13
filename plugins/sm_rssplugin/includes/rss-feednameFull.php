<?php
include('spintax.php');
//$follow='rel="nofollow"';
$smshortname = "ntsmrssar";
if (get_option($smshortname.'_custom_rss_nofollow') == "true") 
	$follow='rel="nofollow"';
else $follow="";
if (get_option($smshortname.'_custom_rss_fulltext') == "true") 
	$full='full';
else $full="";
$beforefeedo=get_option($smshortname.'_custom_rss_beforefeed');//"The post %%POSTLINK%% appeared first on %%BLOGLINK%%.";
//$afterfeed='<p>The post <a rel="'.$follow.'" href="'.the_permalink_rss().'">'.the_title_rss().'</a> appeared first on <a rel="'.$follow.'" href="'.get_site_url().'">'.get_bloginfo("name").'</a>.</p>';
$afterfeedo=get_option($smshortname.'_custom_rss_afterfeed');
$useexcerpt=0;
$spintax = new Spintax();
/**
 * Template Name: Custom RSS Template - Feedname
 */
$postCount = 5; // The number of posts to show in the feed
$posts = query_posts('showposts=' . $postCount);
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
        <?php do_action('rss2_ns'); ?>>
<channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php 
			$lang = get_option('rss_language');
			if ($lang == "") { echo "en-US"; } else { echo get_option('rss_language');} ?></language>
<?php /*        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency> */?>
        <?php do_action('rss2_head'); ?>
        <?php while(have_posts()) : the_post(); ?>
                <item>
                        <title><?php the_title_rss(); ?></title>
                        <link><?php the_permalink_rss(); ?></link>
                        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                        <?php /*<dc:creator><?php the_author(); ?></dc:creator>
                        <guid isPermaLink="false"><?php the_guid(); ?></guid>*/
						/*TODO ispermalink false option !!!*/
						?>
						<guid><?php the_guid(); ?></guid>
						<?php
							$beforereplace='<a '.$follow.' href="'.get_permalink().'">'.get_the_title_rss().'</a>';
							//echo $beforereplace;
							$beforespun= $spintax->process($beforefeedo); 
							$beforefeed =str_ireplace ("%%POSTLINK%%",$beforereplace,$beforespun);
							
							$beforefeed =str_ireplace ("%%BLOGLINK%%",'<a '.$follow.' href="'.get_site_url().'">'.get_bloginfo("name").'</a>',stripslashes($beforefeed)
								);

							$afterspun= $spintax->process($afterfeedo); 
							$afterfeed =str_ireplace ("%%POSTLINK%%",$beforereplace,$afterspun);
							
							$afterfeed =str_ireplace ("%%BLOGLINK%%",'<a '.$follow.' href="'.get_site_url().'">'.get_bloginfo("name").'</a>',stripslashes($afterfeed)
								);								
							
						
								if ($beforefeed != '') {
									$beforefeed = '<p>'.$beforefeed.'</p>';
								}
								if ($afterfeed != '') {
									$afterfeed = '<p>'.$afterfeed.'</p>';
								}
if ($full != '') {								
								$lineexc = $beforefeed . '<p>'.get_the_content().'</p>'.$afterfeed;	
} else {
	$lineexc = $beforefeed . '<p>'.get_the_excerpt().'</p>'.$afterfeed;
}
							?>
						
	                        <description><![CDATA[<?php echo wpautop($lineexc); ?>]]></description>
	                        <content:encoded><![CDATA[<?php echo wpautop($lineexc); ?>]]></content:encoded>
                        <?php rss_enclosure(); ?>
                        <?php do_action('rss2_item'); ?>
                </item>
        <?php endwhile; ?>
		<?php showotherfeeds(); ?>
</channel>
</rss>

<?php
function showotherfeeds()
{
	 // Get RSS Feed(s)
	include_once( ABSPATH . WPINC . '/feed.php' );

	// Get a SimplePie feed object from the specified feed source.
	$flo = get_option($smshortname.'_custom_rss'); 

	foreach(explode("\n", $flo) as $line) {

	//$rss = fetch_feed( 'http://rooftwins.com/newsfeed' );
	$rss = fetch_feed( $line );

	if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

	    // Figure out how many total items there are, but limit it to 5. 
	    $maxitems = $rss->get_item_quantity( 5 ); 

	    // Build an array of all the items, starting with element 0 (first element).
	    $rss_items = $rss->get_items( 0, $maxitems );

	endif;
	?>
    <?php if ( $maxitems == 0 ) : ?>
        <?php echo "";?>
    <?php else : ?>	
        <?php // Loop through each feed item and display each item as a hyperlink. ?>
        <?php foreach ( $rss_items as $item ) :?>	
	
    <item>
            <title><?php echo esc_html( $item->get_title() ); ?></title>
            <link><?php echo esc_url( $item->get_permalink() ); ?></link>
            <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $item->get_date(), false); ?></pubDate>
            <?php /*<dc:creator><?php the_author(); ?></dc:creator>
            <guid isPermaLink="false"><?php the_guid(); ?></guid>*/?>
			<guid><?php the_guid(); ?></guid>
			<?php
				$beforereplace='<a rel="'.$follow.'" href="'.get_permalink().'">'.get_the_title_rss().'</a>';
				echo $beforereplace;
				$beforefeed =str_ireplace ("%%POSTLINK%%",$beforereplace,$beforefeed);
				
				$beforefeed =str_ireplace ("%%BLOGLINK%%",'<a rel="'.$follow.'" href="'.get_site_url().'">'.get_bloginfo("name").'</a>',$beforefeed
					);
					
				$beforefeed = 'The post <a href="http://posturl">Postname</a> appeared first on <a rel="nofollow" href="http://domain.com">http://domain.com</a>';
				$afterfeed = '';
				?>
			
            <description><![CDATA[<?php echo $beforefeed; echo esc_html( $item->get_description() ); echo $afterfeed; ?>]]></description>
            <content:encoded><![CDATA[<?php echo $beforefeed; echo $item->get_content(); echo $afterfeed; ?>]]></content:encoded>
            <?php rss_enclosure(); ?>
            <?php do_action('rss2_item'); ?>
    </item>	
    <?php endforeach; ?>
<?php endif; ?>	
	
	<?php }	
}

/* 
 // Get RSS Feed(s)
include_once( ABSPATH . WPINC . '/feed.php' );

// Get a SimplePie feed object from the specified feed source.
$flo = get_option($smshortname.'_custom_rss'); 
echo "next";
foreach(explode("\n", $flo) as $line) {

//$rss = fetch_feed( 'http://rooftwins.com/newsfeed' );
$rss = fetch_feed( $line );

if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

    // Figure out how many total items there are, but limit it to 5. 
    $maxitems = $rss->get_item_quantity( 5 ); 

    // Build an array of all the items, starting with element 0 (first element).
    $rss_items = $rss->get_items( 0, $maxitems );

endif;
?>
<ul>
    <?php if ( $maxitems == 0 ) : ?>
        <li><?php _e( 'No items', 'my-text-domain' ); ?></li>
    <?php else : ?>
        <?php // Loop through each feed item and display each item as a hyperlink. ?>
        <?php foreach ( $rss_items as $item ) : ?>
            <li>
                <a href="<?php echo esc_url( $item->get_permalink() ); ?>"
                    title="<?php printf( __( 'Posted %s', 'my-text-domain' ), $item->get_date('j F Y | g:i a') ); ?>">
                    <?php echo esc_html( $item->get_title() ); ?>
                </a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<?php } */?>