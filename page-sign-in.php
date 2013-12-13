<?php
/**
 * Template Name: sign-in
 * @package WordPress
 * @subpackage Aucun_lien
 */
get_header(); ?>    

    <div class="content current span-24 last">
        <h2><?php the_title(); ?></h2>
        <form class="span-24 bottom-4 top-2 last" action="<?=get_permalink_by_slug("sign-in")?>" id="sign-in" method="POST">
            <input type="hidden" name="action" value="sign-in" />
            <input name="previous_page" value="<?=$_POST["previous_page"]?>" type="hidden" />
            <label class="span-21 prepend-3 last bottom-1">
                <span class="span-6">Quelle est votre adresse email&nbsp;?</span>    
                <div class="span-5">
                    <input type="text" placeholder="email"  value="<?=$_POST["email"]?>" name="email" class="span-4 text" />
                </div>
                <div class="push-1 field-warning last <?= !in_array("email_format", $form_error) && !in_array("user_unactivated", $form_error) ? 'hidden' : '' ?>">
                    <?php if( in_array("email_format", $form_error) ): ?>
                        L'email n'est pas au bon format.
                    <?php else: ?>
                        Ce compte n'a pas encore été activé.
                    <?php endif; ?>
                </div>
            </label>
            <label class="span-21 prepend-3 last bottom-1">
                <span class="span-6">Quel est votre mot de passe &nbsp;?</span>
                <div class="grey span-5">
                    <input type="password" placeholder="mot de passe" name="password" class="span-4 text" />         
                    <input type="submit" class=" submit" value="" />
                </div>
                <div class="push-1 field-warning last <?= !in_array("incorrect_password", $form_error) ? 'hidden' : '' ?>">
                    Le mot de passe ne correpond pas à l'email.
                </div>
            </label>   
            
            <div class="span-15 prepend-9 bottom-1">                        
                <label><input type="checkbox" name="cookie" /> se souvenir de moi</label> <span class="separator">|</span> <a href="<?php echo get_permalink("forget-password"); ?>">mot de passe oublié ?</a>
            </div>             
        </form>
    </div>

<?php get_footer(); ?>