<?php

/**
 * Webhomeschool Component
 * @package ABlog
 * @subpackage Controllers
 *
 * @copyright (C) 2013 Webhomeschool. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.webhomeschool.de
 * */
// No direct access
defined('_JEXEC') or die();

/**
 * Content component helper.
 *
 * @package Joomla.Administrator
 * @subpackage com_content
 * @since 1.6
 */

require_once JPATH_BASE . '/components/com_ablog/helpers/HTMLPurifier.standalone.php';


abstract class aBlogHelper
{

    public static $extension = 'com_ablog';

    // limit from params
    public static function getComLimitstarter()
    {
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest(self::$extension . 'comlimitstart', 'comlimitstart');
    }

    // limit from params
    public static function getComAnsLimitstarter()
    {
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest(self::$extension . 'comanslimitstart', 'comanslimitstart');
    }

    // limit from params
    public static function getPostsLimitStarter()
    {
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest(self::$extension . 'postslimitstart', 'postslimitstart');
    }

    public static function getPostsEditLimitStarter()
    {
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest(self::$extension . 'postseditlimitstart', 'postseditlimitstart');
    }

    public static function getPostsEditLimit()
    {
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest(self::$extension . 'postseditlimit', 'postseditlimit', 50);
    }

    public static function aBlogEditorTiny()
    {
        $doc = JFactory::getDocument();
        
       $content = '
       		tinymce.init({
       		 		mode: "textarea",
       				force_br_newlines : false,
      				force_p_newlines : false,
       				forced_root_block : "",
                    selector: "textarea",       				
                    theme: "modern",
                    schema: "html5",
       				relative_urls : false,
					remove_script_host : false,
					convert_urls : true,      		
                    add_unload_trigger : false,
                    plugins: ["emoticons wordcount"],      		
                    height: 200,
                    menubar: false,
                    custom_undo_redo_levels: 10,
                    toolbar: "bold italic bullist numlist emoticons undo" 
            });';
        
        if (! is_file(JPATH_BASE . '/components/com_ablog/helpers/tinymce/js/tinymce/tinymce.min.js')) {
            JLog::add(JText::_('JLIB_HTML_EDITOR_CANNOT_LOAD'), JLog::WARNING, 'jerror');
            return false;
        } else {
            $doc->addScript(JUri::base(true) . '/components/com_ablog/helpers/tinymce/js/tinymce/tinymce.min.js');
            $doc->addScriptDeclaration($content);
            return "<textarea id='ablog_content' name='ablog_front_content'></textarea>";
        }
    }
    
    public static function purifyHtmlCode($content){ 
    	$purifier = new HTMLPurifier();
    	return $purifier->purify($content);
    }


    public static function filterText($text)
    {
        return JComponentHelper::filterText($text);
    }

    /*public static function getLinkForSocialButtons($post, $app, $option)
    {

    	$uri = JUri::getInstance(); 
     	if (isset($post->id) >= 1 && ! empty($app) && ! empty($option)) {
            return urlencode("http://".$uri->getHost().JRoute::_("index.php?option=com_ablog&view=post&id=" . $post->id . "&Itemid=" . $app->getUserStateFromRequest($option . 'Itemid', 'Itemid')));
        } else {
            throw new Exception('SocialButtons Data Empty, please contact the Administrator for available support');
        }
    }

    public static function getSystemLanguageForSocialButtons($button)
    {
        $language = JFactory::getLanguage();
        if ($button == 'google' || $button == 'twitter') {
            $tag = $language->getTag();
            return substr($tag, 0, 2);
        } else {
            return str_replace('-', '_', $language->getTag());
        }
    }

    public static function facebookLikeButton($post, $app, $option)
    {
        
    	//echo '<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2F'.self::getLinkForSocialButtons($post,$app,$option).'%2Fdocs%2Fplugins%2F&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;share=false&amp;height=20&amp;appId=648213755290026" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:20px;" allowTransparency="true" id="facebook_button"></iframe>';
        echo '<div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/' . self::getSystemLanguageForSocialButtons('facebook') . '/sdk.js#xfbml=1&appId=648213755290026&version=v2.1";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, \'script\', \'facebook-jssdk\'));</script>

        <div class="fb-like" data-href="' . self::getLinkForSocialButtons($post, $app, $option) . '" data-width="450px" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>';
    }

    public static function twitterButton($post, $app, $option)
    {
        echo '<a class="twitter-share-button" href="https://twitter.com/share"
                data-url="' . self::getLinkForSocialButtons($post, $app, $option) . ' data-lang="' . self::getSystemLanguageForSocialButtons('twitter') . '">
                Tweet </a>
            <script>
window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));
</script>';
    }

    public static function googleButton($post, $app, $option)
    {
        echo '<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: "' . self::getSystemLanguageForSocialButtons('google') . '"}
</script>
<div class="g-plusone" data-annotation="bubble" data-width="60" data-href="' . self::getLinkForSocialButtons($post, $app, $option) . '"></div>';
    }*/
}
