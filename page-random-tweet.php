<?php
/**
 * Template Name: random-tweet
 * @package WordPress
 * @subpackage Aucun_lien
 */
?>            
          
<!DOCTYPE html>
    <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
    <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
    <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
    <!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title>
            <?php
                // Add the blog name.
                bloginfo('name');

                wp_title( '-', true, 'left' );

                // Add the blog description for the home/front page.
                $site_description = get_bloginfo( 'description', 'display' );

                if ( $site_description && ( is_home() || is_front_page() ) ) {
                    echo " - $site_description";
                }
            ?>
        </title>

        <meta name="description" content="<?php bloginfo( 'description'); ?>">

        <meta charset="<?php bloginfo( 'charset' ); ?>" />

        <link rel="profile" href="http://gmpg.org/xfn/11" />        
        <link rel="pingback" href="/xmlrpc.php" />

        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/generic.css">        
        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/screen.css?v1">
                
    </head>
    <?php 
        
        // arguments par default
        $args = array(
            "post_type" => "tweet", 
            "posts_per_page" => 1,
            "orderby" => "rand"
        );            

        query_posts($args);   
        the_post();
    ?>

    <body  id="embed">
        <div class="flux"> 
            <div class="header-tools">
                <?php tweet_tools(); ?>
                <div class="logo">
                    <a href="http://aucun-lien.com" target="_blank">aucun lien</a>
                    <a href="http://www.socult.net" class="socult" target="_blank"><img src="/wp-content/themes/aucun-lien/inc/img/socult-nb.png" alt="powered by So/Cult" /></a>
                </div>
            </div> 

            <?php $author = get_the_author_meta('nickname');  ?>    

            <div class="tweet <?=($line == 1 ? 'page' : '')?> " style="background:<?= tweet_class_color(); ?>">
                <p class="content"><a href="<?php the_permalink(); ?>"  target="_blank"><?php echo get_the_content(); ?></a></p>
                <p class="meta">
                    <a href="/author/<?php echo $author ?>" class="author"><?php echo $author ?></a>
                </p>
            </div>


            <script type="text/javascript" src="/wp-content/themes/aucun-lien/inc/js/jquery-1.7.1.min.js"></script>
            <script type="text/javascript" src="/wp-content/themes/aucun-lien/inc/js/jquery.share.js"></script>
            <script type="text/javascript" src="/wp-content/themes/aucun-lien/inc/js/embed.js"></script>
        </div>
    </body>
</html>