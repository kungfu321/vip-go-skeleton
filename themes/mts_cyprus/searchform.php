<?php
/**
 * The template for displaying the search form.
 *
 * @package Cyprus
 */

$ajax_search = ! empty( cyprus_get_settings( 'mts_ajax_search' ) ) ? ' autocomplete="off"' : ''; ?>

<form method="get" id="searchform" class="search-form" action="<?php echo esc_attr( home_url() ); ?>" _lpchecked="1">
	<fieldset>
		<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" placeholder="<?php esc_html_e( 'Search the site', 'cyprus' ); ?>" <?php echo $ajax_search; ?>>
		<button id="search-image" class="sbutton" type="submit" value="<?php esc_html_e( 'Search', 'cyprus' ); ?>"><?php esc_html_e( 'Search', 'cyprus' ); ?></button>
	</fieldset>
</form>
