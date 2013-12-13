<?php
/**
 * Template Name: contact
 * @package WordPress
 * @subpackage Aucun_lien
 */
get_header(); ?>            
          
    <div class="content current span-24 last">
        
        <?php if( isset($_REQUEST["ok"]) ) : ?>
            <div class="success">Merci ! Votre message nous a bien été transmis.</div>
        <?php endif; ?>

        <?php if (have_posts()) :the_post(); ?>
            <h2><?php the_title(); ?></h2>
            <div class="rich-text">
                <?php the_content(); ?>
            </div>        
        <?php endif; ?>
        <?php edit_post_link('Je suis une page éditable.', '<p class="edit-post">', '</p>'); ?>

        <form class="span-24 bottom-4 top-1 contact" method="POST" action="">
        	<input type="hidden" name="action" value="contact" />
            
            <?php if( !is_connected() ): ?>

                <label class="span-21 bottom-1 prepend-3 last">
                    <span class="span-5">Quelle est votre adresse email&nbsp;?</span>
                    <div class="span-5 last">
                        <input type="text" placeholder="email" name="email" class="text" />
                    </div>
                </label>
                    
            <?php endif ?>

            <label class="span-21 prepend-3 last">
                <span class="span-5">Vous voullez...</span>
                <div class="span-5 last">
                    <select name="subject">
                        <option value="nous faire coucou">nous faire coucou</option>
                        <option value="nous signaler un bug">nous signaler un bug</option>
                        <option value="nous signaler une erreur sur un tweet">nous signaler une erreur sur un tweet</option>
                        <option value="nous suggérer un tweet">nous suggérer un tweet</option>
                        <option value="nous proposer un partenariat">nous proposer un partenariat</option>
                        <option value="que l'un de vos tweets soit supprimé">que l'un de vos tweets soit supprimé</option>
                        <option value="autre chose">autre chose</option>
                    </select>
                </div>
            </label>

            <label class="span-16 prepend-8 top-1 last">                
                <div class="span-10 last">
                    <textarea class="span-10" rows="8" placeholder="exprimez vous ici" name="content"></textarea>
                </div>
            </label>

            <div class="span-16 top-1 prepend-8 last">
                <input type="submit" value="envoyer " class="submit"  />
            </div>
        </form>
    </div>

<?php get_footer(); ?>