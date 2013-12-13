<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */

get_header(); ?>

    <div id="tweet-single" class="content current span-24 last">

    
        <?php while ( have_posts() ) : the_post(); ?>

            <?php $categorie = get_first_category(); ?>
                          
            <div class="single" style="background:<?= tweet_class_color(); ?>">
                <p class="content"><?php echo get_the_content(); ?></p>
                <p class="meta">
                    <a href="<?= get_author_posts_url(get_the_author_meta('ID')); ?>" class="author"><?php the_author(); ?></a>                    
                    <?php /* | <span class="date"><?php the_date(); ?></span> */ ?>
                </p>                
                <?php tweet_tools(); ?>
            </div>
            
            <?php $postID = get_the_ID();   ?>

        <?php endwhile;

        query_posts(
            array(
                "post__not_in" => array($postID),
                "post_type" => "tweet",
                "author" => $post->post_author,
                "posts_per_page" => 5,
                "category__in" => get_moods_ids(), // Avoid uncategorized tweet  
                "paged" => isset($_GET["slot"]) && $_GET["slot"] ? $_GET["slot"] : 1
            )
        );

        $prefix = '<h3 class="span-24 tr">du même auteur</h3>'; 
        html_tweet_flux(1, "flux-author", null, $prefix, true); 
       
        // Disabled : flux "Du même mood"
        if(false && $categorie) {


            query_posts(
                array(
                    "post__not_in" => array($postID),
                    "post_type" => "tweet",
                    "posts_per_page" => 5,
                    "cat" => "{$categorie->term_id}",
                    "paged" => $_GET["slot"] ? $_GET["slot"] : 1
                )
            );

            $prefix = '<h3 class="span-24 tr">du même mood</h3>';
            html_tweet_flux(1, "flux-mood", null, $prefix, true);             
        
        } ?>

    </div>

<?php get_footer(); ?>