<?php
/**
 * Template Name: forget-password
 * @package WordPress
 * @subpackage Aucun_lien
 */
get_header(); ?>            
          
    <div class="content current span-24 last">
        <h2><?php the_title(); ?></h2>
        <p class="intro">Vous avez oublié votre mot de passe ? Pas de panique, on va vous aider.</p>
        <form class="span-24 bottom-4 top-2" method="POST" action="">
        	<input type="hidden" name="action" value="forget-password" />
            <label class="span-21 prepend-3 last">
                <span class="span-6">Quelle est votre adresse email&nbsp;?</span>
                <div class="grey span-5">
                    <input type="text" placeholder="email" name="email" value="<?=$_POST['email']?>" class="span-4 text" />
                    <input type="submit" class=" submit" value="" />
                </div>
                <div class="push-1 field-warning last <?= !in_array("unknown_email", $form_error) ? 'hidden' : '' ?>">
                    Aucun compte ne correspond à cette adresse.
                </div>
            </label>
        </form>
    </div>

<?php get_footer(); ?>