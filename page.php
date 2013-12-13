<?php
/**
 * Template Name: sign-up
 * @package WordPress
 * @subpackage Aucun_lien
 */
get_header(); ?>    

	<div class="content current span-24 last">
	    <?php if (have_posts()) :the_post(); ?>
	        <h2><?php the_title(); ?></h2>
          <div class="container">
  	        <div class="rich-text span-16">
  	            <?php the_content(); ?>
  	        </div>        
          </div>
	    <?php endif; ?>
	    <?php /* edit_post_link('Je suis une page éditable.', '<p class="edit-post">', '</p>'); */ ?>
    </div>

<?php get_footer(); ?>