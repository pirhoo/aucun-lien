<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */

get_header();

    $curauth = $wp_query->get_queried_object(); ?>

    <div class="content current span-24 last">

        <h2>
            Tweets de <a href="http://twitter.com/intent/user?screen_name=<?= $curauth->nickname; ?>" target="_blank">@<?= $curauth->nickname; ?></a>               
            &nbsp;<a href="https://twitter.com/<?= $curauth->nickname; ?>" class="twitter-follow-button left-10" data-show-count="false" data-lang="fr"></a>
        </h2> 

        <?php
        query_posts(
            array(
                "post_type" => "tweet", 
                "posts_per_page" => 6,
                "author" => $curauth->ID,
                "paged" => max(1, $_GET["slot"]),
                'category__in' => get_moods_ids()
            )
        );      

        html_tweet_flux(1, "flux-author"); ?>

    </div>

<?php get_footer(); ?>