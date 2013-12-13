<?php 

// Racine du thème dans l'arborescence
$temp = explode("wp-content/themes/", get_bloginfo("template_url"));
define("THEME_ROOT", get_theme_root()."/".$temp[1]);
// Couleur par défaut des catégories
define("CATEGORY_DEFAULT_COLOR", "#999");

// pour activer ou non le "debug mode"
if( isset($_GET['debug']) ) {
     ini_set('display_errors', 1);
     ini_set('log_errors', 1);
     error_reporting(E_ALL);
} else {
     ini_set('display_errors', 0);
     ini_set('log_errors', 0);
     error_reporting(null);
}

// toutes les erreurs lors de la soumission d'un formumaire sont listées dans ce tableau
$form_error = array();
$form_error = isset($_REQUEST["error"]) ? explode(",", $_REQUEST["error"]) : $form_error;
global $form_error;

// pour connaitre l'état de l'utilisateur (connecté où non)
$isConnected = false;
global $isConnected;

// extra db class
require_once(__DIR__."/inc/lib/aucun-lien/class/Record.class.php");
require_once(__DIR__."/inc/lib/aucun-lien/class/User.class.php");
require_once("phar://".__DIR__."/inc/lib/imagine/imagine.phar");
// managers
require_once(__DIR__."/inc/lib/aucun-lien/functions.user.php");
require_once(__DIR__."/inc/lib/aucun-lien/functions.tweet.php");
// classe d'envoie d'email
require_once(__DIR__."/inc/lib/rmail/Rmail.php");

// Ajoute les tags de pré-rendu
add_action('wp_head', 'html_prerender_tags');
// Action pour l'envoi d'un message via le formulaire de contact
add_action('init', 'send_contact_msg');

// action pour changer le format en sortie d'un tweet
add_action('init', 'catch_search_by_author');

function catch_search_by_author() {
  // Detect search
  if( isset($_GET["s"]) ) {    
    $search = str_replace("@", "", $_GET["s"]);
    $user   = get_user_by("login", $search);
    // User found
    if($user) {
      // Redirect to the user page
      header("Location: /author/".$user->data->user_nicename."/");
      exit;
    }
  }
}

function get_all_moods() {

  static $all_moods = array();
  if( ! empty($all_moods) ) return $all_moods;
  $all_moods = get_categories(
    array('child_of' => 4, 'hide_empty' => true)
  );

  return $all_moods;
}

function get_moods_ids() {
  return array_map( function($m) { 
    return $m->term_id; 
  }, get_all_moods() );         
}

/**
 * @function
 * Donne la première catégorie du post ayant une couleur
 */
function get_first_category() {

	$categories = get_the_category();	
	$categorie = false;
	$i = 0;
	
	do {				
		$categorie = $categories[$i++];		
	} while($i-1 < count($categories) && $categorie->parent != 4);

    // Category exists
    if(count($categorie) > 0) {
	   return $categorie;
    // No category, we set a random one from moods
    } else {
       return set_random_mood(false, true); 
    }
}


function html_prerender_tags() {
	        	
    wp_reset_query();

    // trouve l'url a prefetcher/prerendre selon le type de la page

    if( is_home() ) {		            	

    	$url  = get_bloginfo("wpurl");
    	$url .= "/page/";
    	$url .= max(1, get_query_var('paged') )+1;
    	$url .= "/";

    } elseif( is_author() ) {

    	global $wp_query;

		  $curauth = $wp_query->get_queried_object();  
	          			
      $url  = get_author_posts_url($curauth->ID);
      $url .= '?slot=';
      $url .= max( 1, $_GET["slot"] ) +1;

    } else {			        	
        $url  = get_permalink();
        $url .= '?slot=';
        $url .= max( 1, $_GET["slot"] ) + 1 ;
    }

    ?>
    	<link rel="prefetch"  href="<?=$url;?>" >    	
    	<link rel="prerender" href="<?=$url;?>">

        <meta property="og:site_name" content="aucun lien" />
        <meta property="fb:admins" content="686299757" />
        <meta property="og:image" content="http://aucun-lien.com/wp-content/themes/aucun-lien/inc/img/logo-fb.jpg" />

    <?php if( is_single() ): ?>
        <meta property="og:url" content="<?php the_permalink(); ?>" />
	    <meta property="og:title" content="<?php the_title(); ?>" />
		<meta property="og:type" content="article" />
        <meta property="og:image" content="<?= str_replace(".html", "-250x250.png", get_permalink()) ?>" />    
    <?php endif;

}

function send_contact_msg() {	

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "contact") {
		
		global $user;

		$from  = "aucun lien <noreply@aucun-lien.com>";
		$to    = array("contact@aucun-lien.com");
		$email = is_connected() ? $user->getEmail() : $_POST["email"]; 
		
		$content .= "<p>";
			$content .= "Ce message vous a été transmis depuis le formulaire de contact de <strong>aucun lien</strong>.<br />";
			$content .= "Objet du message : <strong>".strip_tags($_POST["subject"])."</strong><br />";
			if( check_email_address($email) ) { 
				$content .= "Expéditeur : <a href='{$email}'>{$email}</a><br />";
			}
		$content .= "</p>";
		$content .= "<blockquote>";			
			$content .= nl2br( strip_tags($_POST["content"]) );
		$content .= "</blockquote>";


		send_mail($from, $to, "aucun lien : message d'un utilisateur", $content);

		header("Location: " .get_permalink_by_slug("contact")."?ok");
		exit;

	}
	
}


 
/**
 * @function
 * Envoit un mail à l'utilisateur
 */
function send_mail($from, $to, $subject, $content = "") {  
    
    // we have an user
    // we know he didn't confirmed
    // we can create and send the mail
    $email = new Rmail();
    
    // corps du mail
    $emailContent  = "<div id='#aucun-lien-content'>\n";
    	$emailContent .= "<h3 style='margin-bottom:10px;border-bottom:1px solid #363435'>";
    		$emailContent .= "<img src='logo.jpg' alt='aucun lien' />";
    	$emailContent .= "</h3>\n";
    	$emailContent .= "<p>Bonjour,</p>\n";
    		$emailContent .= $content;
    	$emailContent .= "<p>\n";
			$emailContent .= "&Agrave; tr&egrave;s bient&ocirc;t !\n";    		
    	$emailContent .= "</p>\n";    	
    	$emailContent .= "<p style='color:#9e9e9e'>\n";    		
    		$emailContent .= "L'&eacute;quipe <strong>aucun lien</strong>\n";
    	$emailContent .= "</p>\n";
	$emailContent .= "</div>\n";

    // set sender
    $email->setFrom($from);
    
    // set subject
    $email->setSubject( $subject );

    // set text encoding
    $email->setTextCharset("UTF8");
    $email->setHTMLCharset("UTF8");
    $email->setHeadCharset("UTF8");
    
    // set priority
    $email->setPriority("high");
    
    // add logo
    $email->addEmbeddedImage(new fileEmbeddedImage(__DIR__."/inc/img/logo.jpg"));
    
    // set the text content
    $email->setText( strip_tags($emailContent) );
    
    // set the html content
    $email->setHTML( $emailContent );
    
    // send the email
    return $email->send( $to );

}
 
function get_current_URL() {
	return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}


function get_permalink_by_slug($page_slug) {

	// $page = get_page_by_path($page_slug);	
	// return $page ? get_permalink( $page->ID ) : "#";

	return "http://".$_SERVER['SERVER_NAME']."/".$page_slug;

}


function check_email_address($email) {
	// First, we check that there's one @ symbol, 
	// and that the lengths are right.
	if (!preg_match("#^[^@]{1,64}@[^@]{1,255}$#", $email)) {
		// Email invalid because wrong number of characters 
		// in one section or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if
		(!preg_match("#^(([A-Za-z0-9_.-][A-Za-z0-9_.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$#", $local_array[$i])) {
			return false;
		}
	}
	// Check if domain is IP. If not, 
	// it should be valid domain name
	if (!preg_match("#^\[?[0-9\.]+\]?$#", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if(!preg_match("#^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$#", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

function getMonthWord($month) {
	$months = array(
		_("janvier"),
		_("février"),
		_("mars"),
		_("avril"),
		_("mai"),
		_("juin"),
		_("juillet"),
		_("août"),
		_("septembre"),
		_("octobre"),
		_("novembre"),
		_("décembre")
	);
	return $months[$month-1];
	
}

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}



?>