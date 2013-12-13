<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */

get_header(); ?>	
	
    <div class="content current span-24 last">
		<?php
			
		    if( isset($_COOKIE["user_moods"]) && !empty($_COOKIE["user_moods"]) )  $user_moods = explode(",", $_COOKIE["user_moods"]);
		    // if( isset($_COOKIE["user_tags"])  && !empty($_COOKIE["user_tags"]) )   $user_tags  = explode(",",$_COOKIE["user_tags"]);
		    // if( isset($_COOKIE["user_list"])  )   $user_list  = (int) $_COOKIE["user_list"];
		    // if( isset($_COOKIE["user_date"])  )   $user_date  = explode("/",$_COOKIE["user_date"]);
		    // if( isset($_COOKIE["user_time"])  )   $user_time  = explode("h",$_COOKIE["user_time"]);

		    // arguments par default
		    $args = array(
			    "post_type" => "tweet", 
			    "posts_per_page" => 12,
			    "paged" => get_query_var('paged')	? get_query_var('paged') : 1	    
			);

			// filter
			$filter = null;			

			// Doit-on mettre les tweets en désordre ?
			if(!!$_COOKIE["user_random"]) $args += array("orderby" => "rand");	
					
			// Doit-on filtrer les tweets avec les favoris de l'utilisateur ?
			if( is_connected() && !!$_COOKIE["user_bookmarks"] ) {				
				$args += array("post__in" => $user->getBookmarks() );
				// Un tableau de post vide renverait tous les posts, on préfère le remplir d'un post improbable 
				if( count($args["post__in"]) == 0 ) $args["post__in"][] = -1;
			}

			// si il a un list, elle est prioritaire par rapport au reste
		    if( isset($user_list) && !empty($user_list) && $user_list > -1 ) {
		    	
		    	// on les ajoute en argument
		    	$args += array("category__in" => $user_list);

		    } else {

			   	// si il des moods
			    if( isset($user_moods) && count($user_moods) > 0 ) {
			    	// on les ajoute en argument
			    	$args += array("category__in" => $user_moods);	    	
			    }

				/*/
				// si il a des tags
			    if( isset($user_tags) && count($user_tags) > 0 ) {
			    	// on les ajoute en argument
			    	$args += array("tag__in" => $user_tags);
			    } 
			    			    
			    // si il y a une date
			    if( isset($user_date) && count($user_date) > 1 ) {		
					// Hack to previous version of yeay (with two digits)
		            if( $user_date[1] < 2000 ) $user_date[1] += 2000;		    	
			    	$args += array("monthnum" => (int)$user_date[0]);
			    	$args += array("year"  => "20".(int)$user_date[1]);
			    }
				/*/
			    
			    // si il y a une heure
			    /* if( isset($user_time) && count($user_time) > 1 ) {			
			    	// passe l'heure en global pour la transmettre au filtre
			    	global $user_time;
			    	// ajoute un filtre sur la clause where
						add_filter('posts_where', 'filter_where_time');
			    } */
			} 

		    query_posts($args);     		    					    
		    html_tweet_flux(2, "flux-home");
				

	    ?>
	</div>

<?php get_footer(); ?>