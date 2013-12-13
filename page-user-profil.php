<?php
/**
 * Template Name: user-profil
 * @package WordPress
 * @subpackage Aucun_lien
 */
get_header(); ?>   
	
    <div class="content current span-24 last" id="profil">

    	<?php if( isset($_REQUEST["ok"]) ) : ?>
    		<div class="success">Les informations de votre profil ont bien été mises à jour.</div>
    	<?php endif; ?>

        <h2><?php the_title(); ?></h2>

        <?php if( is_connected() ) : ?>

        	<div class="prepend-1 span-23 last">
	        	<h3>Avatar</h3>
	         	<div class="span-13 top-1 avatar">
	         		<div class="span-3">
	         			<img src="http://gravatar.com/avatar/<?=md5($user->getEmail())?>?size=100&d=mm" alt="avatar" />
	         		</div>
	         		<div class="span-9 last rich-text top bottom-1">
	         			<p class="top-1 bottom">
	         				Pour obtenir un avatar sur <strong>aucun lien</strong>, utilisez le service d'avatar universel <a href="http://fr.gravatar.com/" target="_blank">Gravatar</a> !         			
	         			</p>
	         			<p class=" bottom top-1 quiet small">
	         				Ce service est compatible avec la plupart des blogs que vous parcourez sur Internet.
	         			</p>
	         		</div>
	         	</div>
	        </div>

        	<form class="prepend-1 top-2 span-23 last" action="<?=get_permalink_by_slug("profil")?>" method="POST" id="user-update-data">
        		<input name="action" value="user-update-data" type="hidden" />
	        	<h3>Compte</h3>
	            <label class="span-20 last top-1 bottom-1">
	                <span class="span-6">Comment nous devons vous appeler&nbsp;:</span>
	                <div class="span-5">
	                     <input type="text" placeholder="nom et prénom" value="<?=isset($_POST["username"]) ? $_POST["username"] : $user->getName() ?>" name="username" class="span-4 text" />         
	                </div>
	            </label>
	            <label class="span-20 last top-1 bottom-1">
	                <span class="span-6">Votre adresse email&nbsp;:</span>
	                <div class="span-5">
	                    <input type="text" placeholder="email"  value="<?=isset($_POST["email"]) ? $_POST["email"] : $user->getEmail() ?>" name="email" class="span-4 text" />
	                </div>
	                <div class="push-1 field-warning last <?= !in_array("email_exist", $form_error) ? 'hidden' : '' ?>" data-format="L'adresse email n'est pas au bon format.">
	                    L'adresse email est déjà utilisée.
	                </div>
	            </label>      	

	            <label class="span-20 last top-2 bottom-1">
	                <span class="span-6">Mot de passe actuel&nbsp;:</span>
	                <div class="span-5">
	                    <input type="password" placeholder="mot de passe"  value="" name="password-old" class="span-4 text" />
	                </div>
	                <?php if( in_array("password_fail", $form_error) ): ?>
	              		<div class="push-1 field-warning last">Le mot de passe utlisé est incorrect.</div>
	              	<?php endif; ?>
	            </label>
	            <label class="span-20 last top-1 bottom-1">
	                <span class="span-6">Nouveau mot de passe&nbsp;:</span>
	                <div class="span-5">
	                    <input type="password" placeholder="mot de passe"  value="" name="password-new" class="span-4 text" />
	                </div>
	                <div class="push-1 field-warning last hidden" data-format="Trop court (6 caractères minimum)."></div>
	            </label>
	            <p class="span-20 quiet bottom">Pour ne pas modifier votre mot de passe, laissez ces deux champs vides.</p>   

				<div class="top-2 bottom-1 prepend-6 span-16 tl last">
		        	<input type="submit" value="envoyer " class="submit" />
		        </div>
	        </form>

        <?php else : ?>
        	<p class="rich-text">
        		Pour accéder à votre page de profil, veuillez d'abord <a href="<?=get_permalink_by_slug("sign-in")?>">vous connecter</a>. 	
        	</p>
        <?php endif; ?>
    </div> 

<?php get_footer(); ?>