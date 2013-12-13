<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */

get_header(); ?>
        
    <div class="content current span-24 last">

        <h2>Mood <strong><?php single_cat_title(); ?></strong></h2>  
        <?php query_posts(
            array(
                "post_type" => "tweet", 
                "posts_per_page" => 6,
                "cat" => $cat,
                "paged" => get_query_var('paged') ? get_query_var('paged') : 1    
            )
        );        

        html_tweet_flux(1, "flux-category"); ?>

    </div>

<?php get_footer(); ?>