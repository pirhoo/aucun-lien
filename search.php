<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */

get_header(); ?>
        
    <div class="content current span-24 last">
	    <h2>Recherche : <strong><?php the_search_query(); ?></strong></h2>
	    <?php html_tweet_flux(1, "flux-search"); ?>
    </div>

<?php get_footer(); ?>