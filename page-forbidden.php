<?php
/**
/**
 * Template Name: forbidden
 * @package WordPress
 * @subpackage Aucun_lien
 */

get_header(); ?>	
	
    <div class="content current span-24 last">
    	<h2><?php the_title(); ?></h2>
        <div class="rich-text">
            <?php the_content(); ?>
        </div>    
        <?php edit_post_link('Je suis une page éditable.', '<p class="edit-post">', '</p>'); ?>
    	<!--img src="http://httpcats.herokuapp.com/403" alt="403" class="span-20 prepend-2 append-2 black" /-->
	</div>

<?php get_footer(); ?>