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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class CpanelViewPost extends JViewLegacy {

    protected $canDo;

    function display($tpl = null) {

        $this->canDo = ablogHelper::getActions();

        $app = JFactory::getApplication();
        JHtml::stylesheet('admin.css', 'administrator/components/com_ablog/assets/css/');
        JHtml::script('posts.js', '/administrator/components/com_ablog/assets/js/');
        JToolBarHelper::title('', 'blog-48');

        ablogHelper::addSubmenu($app->input->getCmd('act'));

        $user = JFactory::getUser();

        $user_name = $user->name;
        $user_username = $user->username;

        $this->assignRef('user_name', $user_name);
        $this->assignRef('user_username', $user_username);
        JHtml::stylesheet('post.css', 'components/com_ablog/assets/css/');
        //Get Data from PostModel and set into View


        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->addToolBar($app);

        parent::display($tpl);
    }

    function getCommentAnswersById($post_id, $comment_id) {
        $model_comments = & $this->getModel('comments');
        return $model_comments->getCommentAnswersById($post_id, $comment_id);
    }

    function cleanInput($text) {
        return strip_tags(htmlspecialchars($text));
    }

    function addToolBar($app) {
        $task = $app->input->getCmd('task');
        if ($this->canDo->get('core.edit')) {            
            
           JToolBarHelper::save('post.saveEditReturn', 'JTOOLBAR_SAVE');
            
            
            if ($task == 'edit') {
                JToolBarHelper::apply('post.apply_edit', 'JTOOLBAR_APPLY');                
            }else{
                JToolBarHelper::apply('post.apply', 'JTOOLBAR_APPLY');     
            }            
        }
        JToolBarHelper::cancel('post.cancel', 'JTOOLBAR_CANCEL');
    }

}

?>