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
defined('_JEXEC') or die;

/**
 * Content component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
abstract class aBlogHelper {

    public static $extension = 'com_ablog';

    /**
     * Configure the Linkbar.
     *
     * @param	string	$vName	The name of the active view.
     *
     * @return	void
     * @since	1.6
     */
    public static function addSubmenu($vName) {
        JHtmlSidebar::addEntry(
                JText::_('COM_ABLOG_SUB_CPANEL'), 'index.php?option=com_ablog&view=cpanel', $vName == 'cpanel'
        );
        JHtmlSidebar::addEntry(
                JText::_('COM_ABLOG_SUB_BLOG_CATEGORIES'), 'index.php?option=com_ablog&amp;act=blog_categories', $vName == 'blog_categories');
        JHtmlSidebar::addEntry(
                JText::_('COM_ABLOG_SUB_POSTS'), 'index.php?option=com_ablog&amp;act=posts', $vName == 'posts'
        );
        JHtmlSidebar::addEntry(
                JText::_('COM_ABLOG_SUB_COMMENTS'), 'index.php?option=com_ablog&amp;act=comments', $vName == 'comments'
        );
        JHtmlSidebar::addEntry(
                JText::_('COM_ABLOG_SUB_COMMENT_ANSWERS'), 'index.php?option=com_ablog&amp;act=comment_answers', $vName == 'comment_answers'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param	int		The category ID.
     * @param	int		The article ID.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions($messageId = 0) {
    	$user = JFactory::getUser();
    	$result = new JObject;
        //platform deprecated workaround
        $platform_v = JPlatform::getShortVersion();        
     
        if($platform_v < '12.3'){        	
        	$result = new JObject;
        	
        	if (empty($messageId)) {
        		$assetName = 'com_ablog';
        	} else {
        		$assetName = 'com_ablog.message.' . (int) $messageId;
        	}
        	$actions = JAccess::getActions('com_ablog', 'component');
        	foreach ($actions as $action) {
        		$result->set($action->name, $user->authorise($action->name, $assetName));
        	}
        	
        	return $result;
        }else{
        	if (empty($messageId)) {
        		$assetName = 'com_ablog';
        	} else {
        		$assetName = 'com_ablog.message.' . (int) $messageId;
        	}
        	$access_xml_path = JPATH_BASE . '/components/com_ablog/access.xml';        	
        	$actions = JAccess::getActionsFromFile($access_xml_path);
        	foreach ($actions as $action) {        		
        		$result->set($action->name, $user->authorise($action->name, $assetName));        		
        	}
        	return $result;
        }

    }
    
    public static function getLimitStarterABlog(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogabloglimitstart','abloglimitstart');
    }
    
    public static function getLimitABlog(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogabloglimit','abloglimit');
    }
    
    public static function getLimitStarterComments(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogcommentslimitstart','commentslimitstart');
    }
    
    public static function getLimitComments(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogcommentslimit','commentslimit');
    }
    
    public static function getLimitStarterCommentAnswers(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogcomment_answerslimitstart','comment_answerslimitstart');
    }
    
    public static function getLimitCommentAnswers(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogcomment_answerslimit','comment_answerslimit');
    }
    
     public static function getLimitStarterPosts(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogpostslimitstart','postslimitstart');
    }
    
    public static function getLimitPosts(){
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_ablogpostslimit','postslimit');
    }
    
   public static function filterText($text){
        return JComponentHelper::filterText($text);
   }
}