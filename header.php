<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */
?><!DOCTYPE html>
    <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
    <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
    <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
    <!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title>
            <?php
                // Add the blog name.
                bloginfo('name');

                wp_title( '-', true, 'left' );

                // Add the blog description for the home/front page.
                $site_description = get_bloginfo( 'description', 'display' );

                if ( $site_description && ( is_home() || is_front_page() ) ) {
                    echo " - $site_description";
                }
            ?>
        </title>

        <meta name="description" content="<?php bloginfo( 'description'); ?>">

        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=1000" />

        <link rel="profile" href="http://gmpg.org/xfn/11" />        
        <link rel="pingback" href="/xmlrpc.php" />

        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/generic.css">        
        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/jquery.jscrollpane.css">
        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/jquery.jscrollpane.flux.css">
        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/aristo/aristo.css">
        <link rel="stylesheet" href="/wp-content/themes/aucun-lien/inc/css/screen.css?v1">
                
        <!-- Syndication -->
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="/feed?post_type=tweet" />
        <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="/feed/atom?post_type=tweet" />
        
        <?php wp_head(); ?>
    </head>

    <body class="bp">
        
        <header class="<?php if( BETA_MODE && !is_connected() && !is_user_logged_in() ) echo "remove-filters" ?>">
            <h1 class="span-6 logo"><a href="<?php bloginfo('url'); ?>">aucun lien</a></h1>        
            
            <?php /*/ ?>
            <div class="signin-signup span-12 text-right">

                <?php if( is_connected() ) : global $user; ?>
                 
                    <div class="state-signin state">         
                        <div class="span-10">
                            <p><?=$user->getName()?></p>
                            <p><a href="<?php echo get_permalink_by_slug("profil"); ?>">profil</a><span class="separator">|</span><a href="?action=sign-out" class="load">se déconnecter</a></p>
                        </div>               
                        <div class="span-1  last">
                            <img src="http://gravatar.com/avatar/<?=md5($user->getEmail())?>?size=35&d=mm" alt="" />
                        </div>
                    </div>

                <?php else: ?>

                    <div class="state-logout state">
                        <a href="<?php echo get_permalink_by_slug("sign-up"); ?>">s'inscrire</a><span class="separator">|</span><a class="to-login">se connecter</a>
                    </div>
                     
                    <div class="state-login state hidden">         
                        <div class="span-6">
                            <p>
                                <a href="/sign-up">s'inscrire</a><span class="separator">|</span><a class="to-logout">se connecter</a>
                            </p>
                        </div>               
                        <form class="span-6 last" action="<?=get_permalink_by_slug("sign-in")?>" method="POST">  
                            <input type="hidden" name="action" value="sign-in" />
                            <input name="previous_page" value="<?= is_single() ? get_current_URL() : '' ?>" type="hidden" />
                            <p>
                                <label><input type="checkbox" name="cookie" /> se souvenir de moi</label>
                                <span class="separator">|</span>
                                <a href="<?=get_permalink_by_slug("forget-password")?>">mot de passe oublié</a>
                            </p>   
                            <input type="text" placeholder="email" name="email" class="span-6 email last text" />
                            <div class="grey span-6 last">
                                <input type="password" placeholder="password" name="password" class="span-5 text" />
                                <input type="submit" value=""  class="submit"/>
                            </div>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
            <?php /*/ ?>

            <form class="span-6 last search-form">
                <div class="grey span-6 last">
                    <input type="hidden" name="post_type" value="tweet" />
                    <input type="text" placeholder="recherchez..." name="s" class="span-5 text" value="<?= get_search_query() ?>" />
                    <input type="submit" value=""  class="submit"/>
                </div>
            </form>

    

            <?php // CONDITION UNIQUEMENT PENDANT LA BETA
            if( !BETA_MODE || (is_connected() || is_user_logged_in() ) ) : ?>

                <form class="filters span-24" id="filters">
                    <?php /*/ ?>
                    <a class="open"></a>
                    <?php /*/ ?>

                    <?php /*/ ?>
                    <div class="filter lists span-7">
                        <h2>listes</h2>
                        <ul>
                            <?php                                
                                if( isset($_COOKIE["user_list"]) ) {
                                    $user_list = (int) $_COOKIE["user_list"];
                                }

                                $checked = !isset($user_list)  || empty($user_list) || $user_list == -1;
                            ?>
                            <li>
                                <label class="<?= $checked ? "active":"" ?>">
                                    <input 
                                        type="radio"
                                        <?= $checked ? "checked":"" ?>
                                        name="list"
                                        value="-1" />
                                    <strong>Aucune</strong>
                                </label>
                            </li> 

                            <?php 

                            $categories = get_categories(
                                array('child_of' => 40, 'hide_empty' => false)
                            );
                            
                            foreach($categories as $category ): 

                                $checked = isset($user_list) && $user_list == $category->term_id; ?>

                                <li>
                                    <label class="<?= $checked ? "active":"" ?>">
                                        <input
                                            type="radio"
                                            <?= $checked ? "checked":"" ?>
                                            name="list"
                                            value="<?=$category->term_id?>" />
                                        <?=$category->name?>
                                    </label>
                                </li> 
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php /*/ ?>

                    <div class="filter moods span-16">
                        <h2>comment ça va ?</h2>
                        <ul>
                            <?php                             
                                if( isset($_COOKIE["user_moods"]) ) {
                                    $user_moods = explode(",",$_COOKIE["user_moods"]);
                                    // Retro-compatibilité
                                    if( count($user_moods) > 1) $user_moods = $user_moods[0];
                                    else  $user_moods = -1;
                                }

                                //$allChecked = !isset($user_moods) || count($user_moods) == 0 || empty($user_moods[0]);
                                $allChecked = false;
                            ?>
                            <li class="">
                                <label class="<?= $user_moods == -1 ? "active": "" ?>">
                                    <input 
                                        type="radio"
                                        <?= $user_moods == -1 ? "checked" : "" ?>
                                        name="mood"
                                        class="all-moods"
                                        value="-1" />
                                    <strong>Tous</strong>
                                    <span class="square" style="background: <?=CATEGORY_DEFAULT_COLOR?>;"></span>
                                </label>
                            </li> 
                            <?php 

                            $categories = get_all_moods();
                            
                            foreach($categories as $category ): 

                                $checked = isset($user_moods) && ($allChecked || $category->term_id == $user_moods); ?>
                                <li class="span-6">
                                    <label class="<?= $checked ? "active":"" ?>" >
                                        <input
                                            type="radio"
                                            <?= $checked ? "checked":"" ?>
                                            name="mood"
                                            value="<?=$category->term_id?>" />
                                        <?=$category->name?>
                                        <span class="square" style="background: <?= $category->description ?>;"></span>
                                    </label>
                                </li> 
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php /*/ ?>
                    <div class="filter tags span-10 last">
                        <h2>tags</h2>
                        <ul>
                            <?php                                
                                if( isset($_COOKIE["user_tags"]) ) {
                                    $user_tags = explode(",",$_COOKIE["user_tags"]);
                                }

                                $allChecked = !isset($user_tags) || count($user_tags) == 0 || empty($user_tags[0]);
                            ?>
                            <li class="span-5">                            
                                <label class="<?= $allChecked ? "active": "" ?>">
                                    <input
                                        type="checkbox" 
                                        <?= $allChecked ? "allChecked":"" ?>
                                        <?= $allChecked ? "disabled":"" ?>
                                        class="all-tags"
                                        name="tag"
                                        value="-1" />
                                    <strong>Tous</strong>
                                </label>
                            </li> 
                            <?php 

                            $tags = get_tags(
                                array('hide_empty' => true)
                            );
                                                        
                            foreach($tags as $key => $tag ):                                 

                                $checked = isset($user_tags) && ($allChecked || in_array($tag->term_id, $user_tags)); ?>

                                <li class="span-5 <?= $key%2==0 ? 'last' : ''; ?>">
                                    <label class="<?= $checked ? "active":"" ?>">
                                        <input 
                                            type="checkbox" 
                                            <?= $checked ? "checked":"" ?>
                                            name="tag"
                                            value="<?=$tag->term_id?>" />
                                        <?=$tag->name?>
                                    </label>
                                </li> 
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php /*/ ?>
                    
                    <div class="span-4 last random switcher
                        <?= !!$_COOKIE["user_random"] ? "":"off" ?>">
                        <div class="values"></div>
                        <div class="slider"><a class="cursor"></a></div>
                        <span class="label">random</span>   
                    </div>

                    <br class="breaker" />
                </form>

                <?php /*/ ?>
                <div class="navigation span-24">
                                      
                    <?php                    
                        $user_date = -1; isset($_COOKIE["user_date"]) && $_COOKIE["user_date"] != -1 ? explode("/", $_COOKIE["user_date"]) : -1;                    
                        // Hack to previous version of yeay (with two digits)
                        if( $user_date[1] < 2000 ) $user_date[1] += 2000;
                    ?>
                    <div class="span-6 date <?= $user_date != -1 ? "" : "disabled" ?>">
                        <div class="values" 
                             data-month="<?= $user_date != -1 ? $user_date[0] : 01 ?>" 
                             data-year="<?= $user_date != -1 ?  $user_date[1] : date("Y") ?>">
                            <div class="val first">
                                <span class="display"><?= $user_date != -1 ? ($user_date[0] < 10 ? "0":"").$user_date[0] : 01 ?></span>
                                <ul class="picker month hidden">
                                    <li class="current"></li>
                                    <?php for($month=1;$month<=12;$month++): ?>
                                        <li data-month="<?=$month?>"><?=getMonthWord($month)?></li>
                                    <?php endfor; ?>
                                </ul>
                            </div>
                            <div class="val">
                                <span class="display">
                                    <?= $user_date != -1 ? $user_date[1] : date("Y") ?>
                                </span>
                                <ul class="picker year hidden">
                                    <li class="current"></li>
                                    <?php for($year=2010;$year<=date("Y");$year++): ?>
                                        <li data-year="<?=$year?>"><?=$year?></li>
                                    <?php endfor; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="slider"><a class="cursor"></a></div>                                                
                        <label class="label checkbox">
                            <input type="checkbox" 
                                   name="filter-date"
                                   <?= $user_date != -1 ? "checked" : "" ?> /> date</label>
                    </div>

                    <div class="span-6"></div>
                    <?php                    
                        $user_time = isset($_COOKIE["user_time"]) ? $_COOKIE["user_time"] : -1;
                    ?>
                    <div class="span-6 time <?= $user_time != -1 ? "" : "disabled" ?>">
                        <div class="values">
                            <div class="val">
                                <span class="display"><?= $user_time != -1 ? $user_time : "18h00" ?></span>
                                <ul class="picker interval">
                                    <li class="current"></li>  
                                    <li data-time="0h00"  data-word="la nuit">la nuit</li>  
                                    <li data-time="6h00"  data-word="le matin">le matin</li>                             
                                    <li data-time="12h00" data-word="l'après-midi">l'après-midi</li>
                                    <li data-time="18h00" data-word="la soirée">la soirée</li>                            
                                </ul>
                            </div>
                        </div>    
                        <div class="clock" data-time="<?= $user_time != -1 ? $user_time : "18h00" ?>">
                            <img src="<?php bloginfo("template_directory"); ?>/inc/img/hour.png"   alt="" class="hour rotate" />
                            <img src="<?php bloginfo("template_directory"); ?>/inc/img/minute.png" alt="" class="minute rotate" />
                        </div>                    
                        <div class="slider"><a class="cursor"></a></div>
                        <label class="label checkbox">
                            <input type="checkbox" 
                                   name="filter-time" 
                                   <?= $user_time != -1 ? "checked" : "" ?> /> moment de la journée
                        </label>                    
                    </div>

                    <?php if( is_connected() ) : ?>
                        <div class="span-6 stared switcher <?= !!$_COOKIE["user_bookmarks"] ? "":"off" ?> ">
                            <div class="values"></div>
                            <div class="slider"><a class="cursor"></a></div>
                            <span class="label">favoris</span>   
                        </div>                
                    <?php else: ?>
                        <div class="span-6"></div>
                    <?php endif; ?>
                </div>
                <? /*/?>

            <?php endif; ?>

        </header>

        <div class="container" id="container">
            <div id="main" role="main">