<?php
/**
 * Template Name: sign-up
 * @package WordPress
 * @subpackage Aucun_lien
 */
get_header(); ?>    

    <div class="content current span-24 last">
        <h2><?php the_title(); ?></h2>
        <p class="intro">Inscrivez vous en moins d'une minute.</p>        
        <form class="span-24 bottom-4 top-2 last" action="<?=get_permalink_by_slug("sign-up")?>" id="sign-up" method="POST">
            <input type="hidden" name="action" value="sign-up" />
            <label class="span-21 prepend-3 last bottom-1">
                <span class="span-6">Comment vous appelle-t-on&nbsp;?</span>
                <div class="span-5">
                    <input type="text" placeholder="nom et prénom" value="<?=$_POST["username"]?>" name="username" class="span-4 text" />            
                </div>
            </label>
            <label class="span-21 prepend-3 last bottom-1">
                <span class="span-6 required">Quelle est votre adresse email&nbsp;?</span>
                <div class="span-5">
                    <input type="text" placeholder="email"  value="<?=$_POST["email"]?>" name="email" class="span-4 text" />
                </div>
                <div class="push-1 field-warning last <?= !in_array("email_exist", $form_error) ? 'hidden' : '' ?>" data-format="L'adresse email n'est pas au bon format.">
                    L'adresse email est déjà utilisée.
                </div>
            </label>  
            <label class="span-21 prepend-3 last bottom-1">
                <span class="span-6 required">Quel sera votre mot de passe &nbsp;?</span>                        
                <div class="grey span-5">
                    <input type="password" placeholder="mot de passe" name="password" class="span-4 text" />
                    <input type="submit" class=" submit" value="" />
                </div>
                <div class="push-1 field-warning last hidden"  data-format="Trop court (6 caractères minimum).">
                    Trop court (6 caractères minimum).
                </div>
            </label>      
        </form>
    </div>

<?php get_footer(); ?>