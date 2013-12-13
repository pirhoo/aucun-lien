<?php

// Dependance strict de la classe User
if( ! class_exists('User') ) die;

// l'utilisateur courant 
$user = NULL;
global $user;
	
// Activer le mode BETA
define("BETA_MODE", false);

// action pour création d'un utilisateur
add_action('init', 'user_sign_up');
// action pour la connexion d'un utilisateur
add_action('init', 'user_sign_in');
// action pour déconnecter l'utilisateur
add_action('init', 'user_sign_out');
// action pour l'activation d'un compte utilisateur
add_action('init', 'user_activation');
// action pour vérifier qu'un utilisateur est bien connecté
add_action('init', 'user_check_session');
// action pour sauvegarder les filtres de l'utilisateur
add_action('init', 'user_save_filters');
// action pour envoyer un nouveau password à l'utilisateur
add_action('init', 'user_forget_password');
// action pour l'insertion d'un bookmark
add_action('init', 'user_add_bookmark');
// action pour la suppression d'un bookmark
add_action('init', 'user_remove_bookmark');
// action pour mettre à jour les informations de l'utilisateur
add_action('init', 'user_update_data');


/**
 * @function
 * Soumission du formulaire de créeation d'un utilisater
 */
function user_sign_up() {

	global $wpdb, $form_error;			

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "sign-up") {
		
		// si l'email n'est pas bon, on arrête tt
		if(! check_email_address($_POST["email"]) )  {
			$form_error[] = "email_format";		
		
		// VERSION BETA (l'email est valide)
		} else if(! check_email_beta($_POST["email"]) ) {
			$form_error[] = "email_beta";
		}
	

		// si le password n'est pas assez long
		if( strlen($_POST["password"]) < 6 ) {
			$form_error[] = "password_format";		
		}

		// si l'utilisateur existe déjà	
		if(User::findByEmail($_POST["email"], $wpdb) != NULL) {			
			$form_error[] = "email_exist";		
		}

		// si il y a des erreurs
		if( count($form_error) > 0) {			
			// redirige l'utilisateur vers la page d'inscription								
			return;
		}

		// creation de la classe contenant l'utilisateur
		$user = new User(array(
			"name" => $_POST["username"],
			"email" => $_POST["email"],
			"password" => User::crypt_password($_POST["password"]), // chiffrement du mdp
			"activation_key" => md5($_POST["email"] . rand(0,999999999)), // création d'une clef d'activation
			"status" => 1 // pending
		), $wpdb);

		// insère l'utilisateur
		$user->insert();

		// envois la clef d'activation
		user_send_activation_key($user);

		// redirige l'utilisateur vers la page d'attente de confirmation
		header("Location: ".get_permalink_by_slug("sign-up-wait") );
		exit;		
	
	}		
}

/**
 * @function
 * Envois de la clef d'activation à l'utilisateur
 */
function user_send_activation_key($user) {

	$activationUrl = get_permalink_by_slug("sign-up-activation")."?key=".$user->getActivationKey();
	$content .= "<p>\n";
		$content .= "Pour confirmer votre inscription sur aucun lien, veuillez <strong>cliquer sur le lien suivant</strong>&nbsp;:<br />\n";
		$content .= "<a href='{$activationUrl}' target='_blank'>{$activationUrl}</a><br />\n";
	$content .= "</p>\n";

	return user_send_mail($user, "aucun lien : veuillez confirmer votre inscription", $content);        
}

        
/**
 * @function
 * Envoit un mail à l'utilisateur
 */
function user_send_mail($user, $subject, $content) {  
        
    // send the email
    return send_mail("aucun lien <noreply@aucun-lien.com>",  
		    		  array( $user->getEmail() ), 
		    		  $subject, 
		    		  $content);

}
 
/**
 * @function
 * Réception d'une clef pour activation d'un compte utilisateur
 */
function user_activation() {   	
	
	// seulement sur la page sign-up activation
	if( strpos(get_current_URL(), "sign-up-activation" ) && isset($_GET["key"])) {
		
		global $wpdb;

		$user = User::findByActivationKey($_GET["key"], $wpdb);				
		// on a pas trouvé d'utilisateur correspondant
		if($user == null) {
			// redirige l'utilisateur vers la page d'attente de confirmation
			header("Location: ".get_permalink_by_slug("sign-up-failed") );
			exit;
		// on active l'utilisateur
		} else {			
			$user->activate();	
		}
		
	}
}
         

/**
 * @function
 * Connexion d'un utilisateur
 */
function user_sign_in() {

	global $wpdb, $form_error, $user;

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "sign-in") {
				
		// si l'email n'est pas bon, on arrête tt
		if(! check_email_address($_POST["email"]) )  {
			$form_error[] = "email_format";					
		}

		// aucun message d'erreur on peut continuer
		if(count($form_error) == 0) {

			// cherche l'utilisateur
			$user = User::findByEmail($_POST["email"], $wpdb);			
			// l'erreur si on ne le trouve pas elle la même que si le password est incorecte
			if($user == NULL) {			
				$form_error[] = "incorrect_password";
			// on peut continuer est vérifier que le password est le bon
			} else {		
			
				// Si l'utilisateur n'a pas activé son compte
				if( $user->getStatus() != 0) {
					$form_error[] = "user_unactivated";						
				} else {											
					// d'abord on chiffre le password fourni
					$_POST["password"] = User::crypt_password($_POST["password"]);

					// si les deux ne correspondent pas, on s'arrête là
					if($user->getPassword() != $_POST["password"]) {					
						$form_error[] = "incorrect_password";
					// succès ! 
					} else {

						// on connecte l'utilisateur !
						$user->createSession();

						// on restaure ses filtres
						user_restore_filters(); 

						// créé les cookies pour se souvenir de la session
						if($_REQUEST["cookie"] == "on") $user->createCookie();

						// et si elle est spécifiée...
						if( !empty($_POST["previous_page"]) ) {

							// on l'enmène sur la page qu'il visitait auparavant	
							header("Location: ".$_POST["previous_page"] );						

						// ou sinon, sur la page d'acceuil
						} else header("Location: ".get_bloginfo("url") );	

						// et on arrête là
						exit;
					}
				}

			}

		}
				
	}
}

/**
 * @function
 * Déconnecte l'utilisateur
 */
function user_sign_out() {

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "sign-out") {

		global $isConnected;

		// création de la session si non démarée
		if( session_id() == "" ) session_start();

		// on vide les variables de session
		$_SESSION["user_email"] = "";
		$_SESSION["user_password"] = "";
		// on vide les cookies
		setcookie("user_email", NULL, -1); 
		setcookie("user_password", NULL, -1); 
			
		$isConnected = false;

		// retour à l'acceuil
		header("Location: ".get_bloginfo("url") );	
		exit;
	}
}

/**
 * @function
 * L'utilisateur courant est-il connecté ? Si oui, on s'en souvient !
 */
function user_check_session() {

	global $isConnected, $wpdb, $user;

	// création de la session si non démarée
	if( session_id() == "" ) session_start();

	// si les varaibles de session existent
	if( !empty($_SESSION["user_email"]) && !empty($_SESSION["user_password"]) ) {
				
		// cherche l'utilisateur à l'aide de son adresse email
		$user = User::findByEmail($_SESSION["user_email"], $wpdb);
		// si on a bien trouvé l'utilisateur et si le mot de passe et l'utilisater correspondent
		$isConnected = ($user != NULL) && ($user->getStatus() == 0) && ($user->getPassword() == $_SESSION["user_password"]);

	// si les cookies existent
	} elseif( !empty($_COOKIE["user_email"]) && !empty($_COOKIE["user_password"]) ) {

		// cherche l'utilisateur à l'aide de son adresse email
		$user = User::findByEmail($_COOKIE["user_email"], $wpdb);
		// si on a bien trouvé l'utilisateur et si le mot de passe et l'utilisater correspondent
		$isConnected = ($user != NULL) && ($user->getStatus() == 0) && ($user->getPassword() == $_COOKIE["user_password"]);

	} else {						
		$isConnected = false;
	}
	

	// VERSION BETA
	// Si on est pas connecté, pas sur l'admin, pas connecté à l'admin et pas sur la page de connexion 
	if(BETA_MODE && !$isConnected && !is_admin() && !is_user_logged_in() && !is_login_page() ) {

		// Listes des pages autorisées
		$allowedURLs = array(
			home_url()."/wp-login.php",
			home_url()."/wp-admin",
			get_permalink_by_slug("forbidden"),
			get_permalink_by_slug("sign-in"),
			get_permalink_by_slug("forget-password"),
			get_permalink_by_slug("forget-password-success"),
			get_permalink_by_slug("sign-up"),
			get_permalink_by_slug("sign-up-failed"),
			get_permalink_by_slug("sign-up-wait"),
			get_permalink_by_slug("sign-up-activation"),			
			get_permalink_by_slug("contact"),
			get_permalink_by_slug("about"),
			get_permalink_by_slug("privacy-policy"),
			get_permalink_by_slug("credits")
		);

		if( !in_array(get_current_URL(), $allowedURLs) ) {			
			header("Location: ".get_permalink_by_slug("forbidden") );
			exit;
		}
	}	/**/
}


/**
 * @function
 * Enregistre les filtres de l'utilisateur
 */
function user_save_filters() {

	global $user;

	if( is_connected() ) {

		// les filtres sont transmis sous forme de cookies
		$cookiesToSave = array(
			"user_list" => "",
			"user_mood" => "",
			"user_tags" => "",
			"user_random" => "",
			"user_bookmarks" => "",
			"user_date" => "",
			"user_time" => ""
		);		

		// on va simplement copier chaque cookies dans un tableau
		$user->saveFilters( array_intersect_key($_COOKIE, $cookiesToSave) );		
		
	}

}
/**
 * @function
 * Enregistre les filtres de l'utilisateur
 */
function user_restore_filters() {

	global $user;

	// récupère les cookies
	$filters = $user->getFilters();			
		
	if($filters) {
		foreach($filters as $key => $filter) {			
			setCookie($key, $filter, time()+60*60*24*365, "/");
		}
	}

}


/**
 * @function
 * Génère un nouveau password et l'envois par email
 */
function user_forget_password() {

	// si les varaibles de session existent
	if( isset($_REQUEST["action"]) && $_REQUEST["action"] == "forget-password" ) {
		
		global $wpdb, $form_error;

		// cherche l'utilisateur à l'aide de son adresse email
		$user = User::findByEmail($_POST["email"], $wpdb);

		// si on a pas trouvé l'utilisateur
		if($user == NULL) {		
			$form_error[] = "unknown_email";

		// sinon, on peut continuer
		} else {
			
			// corps du mail			
			$content .= "<p>\n";
				$content .= "Pour vous connecter sur aucun lien, voici votre nouveau mot de passe&nbsp;:<br />\n";
				$content .= "<strong>".$user->generateNewPassword()."</strong>\n";			
			$content .= "</p>\n";

			// envoit l'email
			user_send_mail($user, "aucun lien : votre nouveau mot de passe", $content);  			

			// on redirige vers une nouvelle page
			header("Location: ".get_permalink_by_slug("forget-password-success"));
			exit;
		}	
	}

	
}


/**
 * Insert d'un bookmark pour l'utilisateur courant
 *  
 */
function user_add_bookmark() {
	
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add-bookmark") {
		
		global $user;

		// Si l'utilisateur n'est pas connecté on arrête tout.
		if( ! is_connected() ) {
			echo json_encode(array("status" => "ERROR", "msg" => _("User must be connected to add a new bookmark.")));
			exit;
		}

		// Si le format du post passé en paramêtre n'est pas bon
		if( ! is_numeric($_REQUEST["post-id"]) ) {
			echo json_encode(array("status" => "ERROR", "msg" => _("Post format unrecognized.")));
			exit;
		}

		// Si quelque chose survient lors de l'insertion du bookmark
		if( ! $user->addBookmark($_REQUEST["post-id"]) ) {
			echo json_encode(array("status" => "ERROR", "msg" => _("Bookmark unrecorded.")));
			exit;			
		// Sinon, tout c'est bien passé, on envoit le bon message
		} else {
			echo json_encode(array("status" => "OK"));
			exit;
		}


	}

}


/**
 * Suppression d'un bookmark pour l'utilisateur courant
 *  
 */
function user_remove_bookmark() {
	
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "remove-bookmark") {
		
		global $user;

		// Si l'utilisateur n'est pas connecté on arrête tout.
		if( ! is_connected() ) {
			echo json_encode(array("status" => "ERROR", "msg" => _("User must be connected to remove a new bookmark.")));
			exit;
		}

		// Si le format du post passé en paramêtre n'est pas bon
		if( ! is_numeric($_REQUEST["post-id"]) ) {
			echo json_encode(array("status" => "ERROR", "msg" => _("Post format unrecognized.")));
			exit;
		}

		// Si quelque chose survient lors de la suppression du bookmark
		if( ! $user->removeBookmark($_REQUEST["post-id"]) ) {
			echo json_encode(array("status" => "ERROR", "msg" => _("Bookmark unremoved.")));
			exit;			
		// Sinon, tout c'est bien passé, on envoit le bon message
		} else {
			echo json_encode(array("status" => "OK"));
			exit;
		}

	}
}
/**
 * Mise à jour des informations de l'utilisateur
 *  
 */
function user_update_data() {
	
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "user-update-data") {
		
		global $user, $form_error, $wpdb;

		// Si l'utilisateur n'est pas connecté on arrête tout.
		if( ! is_connected() ) {
			// redirige l'utilisateur vers la page de connexin
			header("Location: ".get_permalink_by_slug("sign-in") );
			exit;
		}

		// si l'email n'est pas bon, on arrête tt
		if(! check_email_address($_POST["email"]) )  {
			$form_error[] = "email_format";		
		}

		// si l'utilisateur existe déjà	
		if($user->getEmail() != $_POST["email"] && User::findByEmail($_POST["email"], $wpdb) != NULL) {			
			$form_error[] = "email_exist";			
		} else {
			// on enregistre le nouvel email
			$user->setEmail($_POST["email"]); 
		}


		// si le password n'est pas assez long
		if( !empty($_POST["password-old"]) && User::crypt_password($_POST["password-old"]) != $user->getPassword() ) {
			$form_error[] = "password_fail";		
		}

		// si le password n'est pas assez long
		if( !empty($_POST["password-new"]) && strlen($_POST["password-new"]) < 6 ) {
			$form_error[] = "password_format";					
		// ou si l'ancien password correspond bien
		} elseif( !empty($_POST["password-new"]) && !in_array("password_fail", $form_error) ){
			// on enregistre le nouveau password
			$user->setPassword( User::crypt_password($_POST["password-new"]) );		
		}


		// tout va bien
		if( count($form_error) == 0 ) {						
			$user->setName($_POST["username"]);	
			// on met à jour les données
			$user->sync();
			// recomplete les variables de session
			$user->createSession();
			// et si des cookies existent, on les met à jour
			if(isset($_COOKIE["user_email"])) $user->createCookie();
			// on redirige
			header("Location: ".get_permalink_by_slug("profil")."?ok" );
			exit;
		}

	}
}

/**
 * @function
 * Pour savoir si l'utilisateur est bien connecté
 * @return boolean
 */
function is_connected() {
	global $isConnected;
	return !!$isConnected;
}



function check_email_beta($email) {

	global $wpdb;

	// Saute cette vérificatio
	if(! BETA_MODE ) return true;

	// l'email existe-t-il déjà ?
	$email_count = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM user WHERE email = '{$email}' AND id <= 45" ) );

	// l'email doit exister
	return $email_count > 0;
}

?>