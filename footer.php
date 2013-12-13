 <?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Aucun_lien
 */
?>              
                <br class="clear" />
                <div class="loader"></div>                
            </div> <!--! end of #main -->

            <footer>
                <div class="span-16 left-side">
                    <a href="<?php bloginfo('url'); ?>" class="site-title">aucun lien</a>
                    <a href="http://www.socult.net" class="so-cult" target="_blank"><img src="/wp-content/themes/aucun-lien/inc/img/poweredbySC.png" alt="powered by so/cult" /></a>
                    <div class="links">
                        <a  href="<?=get_permalink_by_slug("about")?>" >à propos</a>
                        <span class="separator">|</span>
                        <a  href="<?=get_permalink_by_slug("contact")?>" >contact</a>
                        <span class="separator">|</span>
                        <a  href="<?=get_permalink_by_slug("cgu")?>" >cgu</a>
                        <span class="separator">|</span>
                        <a  href="<?=get_permalink_by_slug("credits")?>" >crédits</a>
                        <span class="separator">|</span>
                        <a  href="<?=get_permalink_by_slug("charte")?>" >nous signaler des choses</a>
                    </div>
                </div>
                <div class="span-8 last share">
                    <span class="site-title">
                        <br />
                        <a href="http://twitter.com/aucun_lien" target="_blank">nous suivre sur<br />twitter</a>
                        <!--br /><a href="" target="_blank">nous aimez sur<br />facebook</a>
                        <a href=""><img src="<?php bloginfo("template_directory"); ?>/inc/img/ico-facebook.png" alt="facebook" /></a-->
                        <a href="http://twitter.com/aucun_lien" target="_blank"><img src="<?php bloginfo("template_directory"); ?>/inc/img/ico-twitter.png" alt="twitter" /></a>
                        <a href="<?php bloginfo('rss2_url'); ?>?post_type=tweet"><img src="<?php bloginfo("template_directory"); ?>/inc/img/ico-rss.png" alt="rss" /></a>
                    </span>
                </div>
            </footer>

        </div> <!--! end of #container -->


        <?php if( false && !isset($_COOKIE["alerte-beta"]) || isset($_REQUEST["force-alerte"]) ): ?>
            <?php $alerte = get_page_by_path("alerte-beta"); ?>
            <?php if($alerte) : ?>
                <div cass="js-fg-group">
                    <div class="js-overlay js-close-fg"></div>
                    <div class="js-popup">
                        <h2><?php echo $alerte->post_title;?></h2>
                        <div class="rich-text">                
                            <?php echo apply_filters('the_content', $alerte->post_content);  ?>
                        </div>
                        <div class="text-right top-2">
                            <a class="btn js-close-fg">Merci !</a>
                        </div>
                    </div>
                </div>
                <?php setcookie("alerte-beta", 60*60*24*365); ?>
            <?php endif; ?>            
        <?php endif; ?>


        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script>window.jQuery && 0 || document.write('<script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery-1.7.1.min.js"><\/script>');</script>

        <?php /*
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <script>window.jQuery.ui || document.write('<script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery-ui-1.8.16.min.js"><\/script>');</script>
        */ ?>
    
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery.mousewheel.min.js"></script>  
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/modernizr-2.0.6.min.js"></script>   
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/mwheelIntent.min.js"></script>         
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery.jscrollpane.min.js"></script> 
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery.rotate.min.js"></script> 
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery.placeholder-enhanced.min.js"></script>        
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery.cookie.min.js"></script>
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/jquery.share.js"></script>

        <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
        
        <script src="<?php bloginfo("template_directory"); ?>/inc/js/global.js"></script>          

        <!--[if lt IE 7 ]>
            <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
            <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
        <![endif]-->

        <?php wp_footer(); ?>

        <?php /* echo get_num_queries(); ?> queries in <?php timer_stop(1); ?> seconds. <!--*/?><!---->

    </body>
</html>
