<?php

// Dependance strict de Imagine
// @src http://imagine.readthedocs.org
if( ! interface_exists('Imagine\Image\ImageInterface') ) die;

// action pour création des post types
add_action('init', 'create_post_type');
// Determine la structure des permalink
add_action('init', 'tweet_rewrite_rules');
// filtre lors de l'importation d'un post
add_filter('syndicated_post', 'clean_feedwordpress' );
// Complète le permalink des tweets
add_filter('post_type_link', 'tweet_link', 1, 3);
// action pour changer le format en sortie d'un tweet
add_action('init', 'catch_tweet_format');
// action pour utiliser les tweets dans la requête principale
add_filter('pre_get_posts', 'my_get_posts' );


if( isset($_GET["show_duplicates"]) ) show_duplicates();
if( isset($_GET["remove_duplicates"]) ) remove_duplicates();
if( isset($_GET["show_empty_posts"]) ) show_empty_posts();
if( isset($_GET["remove_empty_posts"]) ) remove_empty_posts();

function get_empty_posts() {

  global $wpdb;

  $query   = array();
  $query []= "SELECT ID";
  $query []= "FROM $wpdb->posts";
  $query []= "WHERE post_content = ''";
  $query []= "AND post_type = 'post'";

  return $wpdb->get_results( join($query, "\n") );
}

function show_empty_posts($empty=false) {

  if(!$empty) $empty = get_empty_posts();
  echo "<pre>";
    print_r($empty);
  echo "</pre>";
  exit;

}

function remove_empty_posts() {

  global $wpdb;
  $empty = get_empty_posts();

  $query   = array();
  $query []= "DELETE FROM $wpdb->posts";
  $query []= "WHERE post_content = ''";
  $query []= "AND post_type = 'post'";

  $wpdb->get_results( join($query, "\n") );

  echo "<pre>";
    print_r($empty);
  echo "</pre>";
  exit;
}


function get_duplicates() {

  global $wpdb;

  $query   = array();
  $query []= "SELECT ID, post_title, count(post_title) as nb";
  $query []= "FROM $wpdb->posts";
  $query []= "WHERE post_status = 'publish'";
  $query []= "AND post_type = 'tweet'";
  $query []= "GROUP BY post_title";
  $query []= "HAVING nb > 1";

  return $wpdb->get_results( join($query, "\n") );
}

function show_duplicates($duplicates=false) {

  if(!$duplicates) $duplicates = get_duplicates();

  echo "<pre>";
    print_r($duplicates);
  echo "</pre>";
  exit;
}

function remove_duplicates() {

  global $wpdb;

  $duplicates = get_duplicates();

  foreach($duplicates as $p) {    

    $title = mysql_real_escape_string($p->post_title);
    $limit = $p->nb - 1;

    $query   = array();
    $query []= "DELETE FROM $wpdb->posts";
    $query []= "WHERE post_status = 'publish'";
    $query []= "AND post_type = 'tweet'";
    $query []= "AND post_title = '{$title}'";
    $query []= "LIMIT {$limit}";

    $rows = $wpdb->get_results( join($query, "\n") );     
  }

  show_duplicates($duplicates);
}

/**
 * Set a random mood to the given post (if no mood)
 * @param Object $post
 * @param Boolean $override
 */
function set_random_mood($p=false, $override=false) {
  

  if($p === false) {
    global $post;

    $p = $post;    
    if(! $override) $categories = get_the_category(); 

  } else if(! $override) {
    $categories = get_the_category($p->ID); 
  }

  // No categories for this post, 
  // we have to set one
  if( $override || count($categories) === 0 ) {
    // Gets a random mood
    $moods = get_all_moods();
    $mood = $moods[array_rand($moods)];

    // Sets the mood
    wp_set_post_categories($p->ID, array($mood->term_id));

    // FLush object cache
    //w3tc_objectcache_flush();

    return $mood;
  }

  return $categories;

}

/**
 * Change la requête principale pour y ajouter les tweet
 * @param  Object $query
 * @return Object
 */
function my_get_posts( $query ) {

  if ( $query->is_main_query() && ( is_home() ) ) {
    $query->set( 'post_type', array('post', 'tweet') );
  }

  return $query;
}


/**
 * @function
 * Création des post type, seulement "tweet" pour le moment
 */
function create_post_type() {

	register_post_type('tweet',
		array(
			'labels' => array(
				'name' => __( 'Tweets' ),
				'singular_name' => __( 'Tweet' )
			),		
			'public' => true,
			'has_archive' => false,
			'menu_position' => 5,
			'supports' => array('title','editor','author','custom-fields'),
			'taxonomies' => array('category','post_tag'),
      'menu_icon' => get_bloginfo("template_url").'/inc/img/tweet.png',
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => array("slug" => "tweet")
		)
	);
}


function extract_aucunlien( $str ) {


	$regex1 =  "#^(Aucun\sLien:)?\s*(\"|'|“)(.*)(\"|'|”)(\s*)@([a-z0-9_]+)#i";
	$regex2 =  "#^(Aucun\sLien:)?\s*“@([A-Za-z0-9_]+)(\s?:?\s?)(.*)”$#i";
	$regex3 =  "#^(Aucun\sLien:)?\s*RT\s*@([A-Za-z0-9_]+)(\s?:?\s?)(.*)$#i";
	

	switch(true) {

		// Tweet sous la forme : "bloublou bloublou" @pirhoo
		case preg_match($regex1, $str) :			
			preg_match_all($regex1, $str, $arr);
			return array(
				"author"	=> trim($arr[6][0]),
				"content"	=> trim($arr[3][0])
			);
			break;

		// Tweet sour la forme : “@pirhoo : bloublou blublu”
		case preg_match($regex2, $str) :				
			preg_match_all($regex2, $str, $arr);
			return array(
				"author"	=> trim($arr[2][0]),
				"content"	=> trim($arr[4][0])
			);
			break;

		// Tweet sour la forme : RT @pirhoo : bloublou
		case preg_match($regex3, $str) :			
			preg_match_all($regex3, $str, $arr);
			return array(
				"author"	=> trim($arr[2][0]),
				"content"	=> trim($arr[4][0])
			);
			break;



		default:
			return false;
			break; 
	}
}


/**
 * @function
 * Importation d'un post
 */
function clean_feedwordpress ( $data ) {
	
	$regex = "#^\"?.*@([A-Za-z0-9_]+)(\s?:?\s?)(.*)\"?$#i";
	$tweet = extract_aucunlien($data["post_content"]);
    
  if($tweet === false) {
    
    // Reject the tweet
    return null;

  } else {
    
		// extrait l'auteur du tweet
		$user_name = $tweet["author"];
		// extrait le contenu seul du tweet
		$data["post_excerpt"] = $data["post_content"] = $data["post_title"] = $tweet["content"];	

    // Skips existing tweet
    if( !! get_page_by_title($data["post_title"], 'OBJECT', "tweet") ) return null;	  	

		// cherche l'user id the l'auteur pour savoir si il existe déjà
		$user_id = username_exists($user_name);	
		// si non
		if ( !$user_id ) {
			$user_name;
			// génère un password
			$random_password = wp_generate_password( 12, false );
			// et créé le nouvel auteur !
			$user_id = wp_create_user($user_name, $random_password, $user_name."@no.where");		
		}	

		// assigne ensuite le tweet l'auteur (tout juste créé ou non)
		$data["post_author"] = $user_id;

		return $data;
	}
}


/**
 * @function
 * Determine la structure des permalink
 */
function tweet_rewrite_rules(){	
  	$queryarg = 'post_type=tweet&p=';
    add_rewrite_tag("%id%", '(\d+)', $queryarg);
    add_permastruct('tweet', '/tweet/%id%.html', true);    
    flush_rewrite_rules( true );
}

/**
 * @function
 * Complète le permalink des tweets
 */
function tweet_link($post_link, $post = 0, $leavename = false) {
		
	if(!is_object($post) || $post->post_type != "tweet" ) {
		return $post_link;
	}
	
	return str_replace("%id%", $post->ID, $post_link);	
}


/**
 * @function
 * Détermine quelle classe appliquer au tweet (pour changer sa couleur)
 */
function tweet_class_color() {

	$categorie = get_first_category();	    
	return 	$categorie ? $categorie->description : CATEGORY_DEFAULT_COLOR;

}


/**
 * @function
 * Si il y a des post dans le loop, les affiche tel un flux de tweet
 */
function html_tweet_flux($line = 2, $id = "", $filter = null, $prefix = "", $hideIfEmpty = false) {

	global $post, $wp_query, $user;	

	if( is_connected() ) $bookmarks = $user->getBookmarks();
	// iterateur
	$i = $count = 0;

  if( ! have_posts() && $hideIfEmpty ) return;
  
  echo $prefix; ?>

  <div class="flux span-24" id="<?=$id?>">
    
    <div class="wrapper last">            	

		  <?php while(have_posts()) : the_post(); wp_reset_postdata(); ++$count; ?>
		    	
		    	<?php					    		
		    		// Si il y a une liste de post filtrés et que le courant n'est pas dedans on saute au suivant.
		    		if( is_array($filter) && !in_array($post->ID, $filter) ) continue;
		    	?>
				
				<?php if($i == 0 && $line > 1) : ?>
					<ul  class="span-8 page">
				<?php endif; ?>

                    <?php $author = get_the_author_meta('nickname');  ?>      
                    <li class="tweet no-sized span-8 <?=($line == 1 ? 'page' : '')?> " style="background:<?= tweet_class_color(); ?>">
        	            <p class="content"><a href="<?php the_permalink(); ?>"><?php echo get_the_content(); ?></a></p>
        	            <p class="meta">
        	            	<a href="/author/<?php echo $author ?>" class="author"><?php echo $author ?></a>                            
        	            </p>
                      <?php tweet_tools($post); ?>
        	        </li>

				<?php if($i == $line-1  && $line > 1) : $i=0; ?>
					</ul>
				<?php else: $i++; ?>

				<?php endif; ?>

			<?php endwhile; ?>

			<?php if($i <= $line-1 ): ?></ul><?php endif; ?>

			<?php if($count == 0 && !$hideIfEmpty) : ?>
				<div class="tweet span-24 tc white">
					<p class="top-10 bottom">Aucun tweet ne correspond à votre demande.</p>						
					<p class="quiet top">Pour obtenir plus de résultats, réduisez le nombre de filtres.</p>						
				</div>
			<?php else : ?>
			
				<ul class="span-8 page loading">					
					<li class="tweet"></li>						
				</ul>
								
			<?php endif; ?>

    	<!--br class="breaker" /-->
    </div>

    <div class="pagination fhidden">
        <?php    
        	
	        wp_reset_query();

            if( is_home() ) {		            	

	            echo paginate_links( array(
	                'base' => "%_%",
	                'format' => get_bloginfo("wpurl")."/page/%#%/",
	                'current' => max( 1, get_query_var('paged') ),
	                'total' => $wp_query->max_num_pages
	            ) );

	        } elseif( is_author() ) {

        			$curauth = $wp_query->get_queried_object();            			

	            echo paginate_links( array(
	                'base' => "%_%",
	                'format' => get_author_posts_url($curauth->ID).'?slot=%#%',
	                'current' => max( 1, $_GET["slot"] ),
	                'total' => $wp_query->max_num_pages
	            ) );

	        } else {			        	

	            echo paginate_links( array(
	                'base' => "%_%",
	                'format' => get_permalink().'?slot=%#%',
	                'current' => max( 1, $_GET["slot"] ),
	                'total' => $wp_query->max_num_pages
	            ) );

	        }
        ?>
	    </div>	

    </div>

	<?php 

}

function tweet_tools($p = null) {

  global $user;

	if($p != null) $post = $p;
	else global $post;

	?>
  <ul class="tools"> 
      <?php
          $permalink = get_permalink();
          $image = str_replace(".html", ".png", $permalink);
          $title = get_the_title();

          $tweetPermalink = get_post_custom_values("syndication_permalink");
          $tweetPermalink = $tweetPermalink[0];
          $tweetId = preg_replace("/.+\/(\d+)$/i", "$1", $tweetPermalink); 
      ?>

      <?php if( is_connected() ): ?>
          <li>
              <a data-post-id="<?=$post->ID?>"
              	 href="<?=$permalink?>"
                 class="bw-icon bookmark <?= in_array($post->ID, $user->getBookmarks() ) ? 'on' : '' ?>">
                 Mettre favoris
              </a>
          </li>
      <?php endif; ?>                    
      <li>
          <a href="<?=$permalink?>" 
          	 class="fb"
             data-url="<?=$permalink?>"> 
              <img src="<?php bloginfo("template_directory"); ?>/inc/img/bw-facebook.png" alt="facebook" />
              <!--span class="count">∞</span-->
          </a>
      </li>
      <li>
          <a class="tw" 
             href="<?=$permalink?>"
             data-tweet-id="<?=$tweetId?>"                           
             data-related="aucun_lien,<?php the_author(); ?>">
              <img src="<?php bloginfo("template_directory"); ?>/inc/img/bw-twitter.png" alt="twitter" />
              <!--span class="count">∞</span-->
          </a>
      </li>
      <li>
          <a href="<?=$permalink?>"
             class="pn"
             data-text="<?=$title?>"
             data-media="<?=$image?>"
             data-url="<?=$permalink?>">   
              <img src="<?php bloginfo("template_directory"); ?>/inc/img/bw-pinterest.png" alt="facebook" />
          </a>
      </li>
      <li>
          <a href="<?=$permalink?>"
             class="gp"
             data-text="<?=$title?>"
             data-media="<?=$image?>"
             data-url="<?=$permalink?>">   
              <img src="<?php bloginfo("template_directory"); ?>/inc/img/bw-google.png" alt="facebook" />
          </a>
      </li>
  </ul>
	<?php
}


/**
 * @function
 * Action pour changer le format en sortie d'un tweet
 */
function catch_tweet_format() {

	// On est sur la single des tweets
	if( isset($_GET["post_type"]) && $_GET["post_type"] == "tweet" && is_numeric($_GET["p"]) ) {		

		switch( $_GET["format"] ) {

			case "png":
				send_tweet_image($_GET["p"], $_GET["w"], $_GET["h"]);
				exit;
				break;
			
			case "html":
				// rien du tout, c'est bien
				break;

			default:			
				header("Location: ".get_permalink($_GET["p"]));				
				exit;
		}
	}
}


/**
 * @function
 * Filtre conditionel pour restreindre l'affichage du flux global à un certain interval de temps
 */
function filter_where_time($where = '') {						

	// récupère le l'heure passée en globale
	global $user_time;

	$startTime = (int)$user_time[0];
	$endTime   = $startTime + 6;

	$where .= "AND HOUR(wp_posts.post_date) >='{$startTime}'";
	$where .= "AND HOUR(wp_posts.post_date)  <'{$endTime}'";
	
	return $where;
}




/**
 * @function
 * Envois un tweet au format png
 */
function send_tweet_image($p, $w = 800, $h = 400) {
    
    global $wpdb, $post;

    // Vérifie que la largeur et la hauteur soient bons
    $w = (int) ( $w >= 250 && $w <= 3000 ? $w : 800);
    $h = (int) ( $h >= 250 && $h <= 3000 ? $h : 400);
    
    // Trouve le post post
    $post = get_post($p);
    
    // Pas post = 404 !
    if(!$post) {
        header("Location: /404");
        exit;
    }

    // Instancie Imagine avec GD
    $imagine = new Imagine\Gd\Imagine();
    // Taille de texte la plus haute
    $fontSize = 150;
    // Couleurs
    $colors = Array(
        "background" => new Imagine\Image\Color( tweet_class_color() ),
        "white"      => new Imagine\Image\Color('fff'),
        "black"      => new Imagine\Image\Color('000')
    );      
    // Taille de l'image
    $size = new Imagine\Image\Box($w, $h);  
    // Padding horizontal et vertical
    $padding = array("x" => $size->getWidth()*0.04, "y" => $size->getWidth()*0.04);

    do {
        // Creation de l'image
        $image = $imagine->create($size, $colors["background"]);
        // Font du tweet
        $font =  $imagine->font(THEME_ROOT."/inc/font/MEMPHISM.TTF", $fontSize, $colors["white"]);  
        // Hauteur des lignes
        $lineHeight = $font->getSize()*1.2;
        $lineWidth = 0;
        
        $tweet = nl2br(html_entity_decode( get_the_title(), ENT_QUOTES, "UTF-8" ) );
        // Tweet avec les sauts de ligne        
        $tweet = img_wordwrap($tweet, $size->getWidth() - $padding["x"]*2, $font->getSize(), $font->getFile() );
        
        // Sépare chaque ligne du tweet
        $tweet = explode("\n", $tweet);

        // Ecrit le tweet, ligne a ligne
        foreach($tweet as $key => $line) {
            $point = new Imagine\Image\Point($padding["x"], ($lineHeight * $key) );

            $lineWidth  = max($lineWidth, $font->getSize()*0.75 * strlen($line) );
            // On ajoute un pipe à la chaine pour que toutes les lignes aient la hauteur maximal
            $image->draw()->text(add_end_pipe($line), $font, $point );
        }

        // Si on doit refaire le calcul 
        $fontSize-= ceil($fontSize/15);
        
    } while( 
        (
            count($tweet) * $lineHeight > $size->getHeight() - $padding["y"] * 2 - 15    
            //|| $lineWidth > $w + $padding["y"] * 2
        )
        && $fontSize > 0
    );


    $authorSize = 11; // pt
    $authorPos  = new Imagine\Image\Point($padding["x"], $h-$padding["y"]-$authorSize*1.25);
    $authorFont = $imagine->font(THEME_ROOT."/inc/font/HelveticaNeueLTCom-Bd.ttf", $authorSize, $colors["black"]);
    $authorNick = "@".get_user_meta($post->post_author, "nickname", true);
    // Ajoute le nom de l'auteur 
    $image->draw()->text($authorNick, $authorFont, $authorPos);

    if($w >= 200) {        
        $logoSize  = 12; // pt
        $logoWidth = 85;
        $logoPos   = new Imagine\Image\Point( $w - $padding["x"] - $logoWidth, $h - $padding["y"] - 13.25);
        $logoFont  = $imagine->font(THEME_ROOT."/inc/font/MEMPHISM.TTF", $logoSize, $colors["black"]);    
        // Ajoute le nom de l'auteur 
        $image->draw()->text("aucun lien", $logoFont, $logoPos);
    }
  
    // Affiche l'image au format jpg
    $image->show("png");
}


function img_wordwrap($string, $width, $fontSize, $fontFace, $angle = 0){
       
    $ret = "";
   
    $arr = explode(' ', $string);
   
    foreach ( $arr as $word ){
   
        $teststring = $ret." ".$word;
        $testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);

        if ( $testbox[2] > $width ) {        	
            $ret .= ( $ret == "" ? "":"\n") . $word;
        } else {
            $ret .= ( $ret == "" ? "":' ')  . $word;
        }
    }
   
    return $ret;
}


function add_end_pipe($str = "") {
	// Ajoute 100 espaces
	for($i=0; $i < 100; $i++) $str.=" ";
	// Tout un tas de caractères spéciaux qui peuvent être peuvent atteindres la hauteur de ligne maximale
	return $str.",;:=?./+&é'(§è!çà)|[]";
}
	
?>