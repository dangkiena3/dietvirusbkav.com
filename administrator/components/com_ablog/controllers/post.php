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
defined('_JEXEC') or die('Restricted acess');
jimport('joomla.application.component.controller');



class CpanelControllerPost extends JControllerLegacy {
	

    //If categories for PostView are loaded, show post
    //else create any
    function add() {

        $model_categories = $this->getModel('blog_categories');
        $kategories = $model_categories->getCategoriesTree();

        if ($kategories) {
            $view = $this->getView('post', 'html');
            $model_posts = $this->getModel('posts');
            $view->setModel($model_posts);
            $view->assignRef('kategories', $kategories);
            $view->display();
        } else {
            $this->setRedirect('index.php?option=com_ablog&act=posts');
            JError::raiseNotice(100, JText::_('Please create a Categorie'));
            return false;
        }
    }

    function remove() {
        $model = $this->getModel('posts');
        $cids = JFactory::getApplication()->input->getVar('cid', '', 'post', 'array');
        $model->delete($cids);
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }

    function saveEditReturn() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('posts');
        $model->storeEdit();
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }

    function apply() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('posts');
        $model->storeEdit();
        $post_id = JFactory::getApplication()->input->get('post_id', '', 'int');        
        $this->setRedirect('index.php?option=com_ablog&act=post&task=add');
    }
    
    function apply_edit() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('posts');
        $model->storeEdit();
        $post_id = JFactory::getApplication()->input->get('post_id', '', 'int');

        if ($post_id) {
            $this->setRedirect('index.php?option=com_ablog&act=posts&task=edit&cid=' . (int) $post_id);
        } else {
            $post_id = $model->getLastInsertedPostId();
            $this->setRedirect('index.php?option=com_ablog&act=posts&task=edit&cid=' . (int) $post_id);
        }
    }

    function cancel() {
        $model = $this->getModel('posts');
        $model->checkIn();
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }

    private function handleEditCheckouts($model) {
        $user = JFactory::getUser();
        if ($model->checkIsCheckedOut() != $user->id) {
            $this->setRedirect('index.php?option=com_ablog&act=posts', 'Check-out failed with the following error: The user checking out does not match the user who checked out the item. ');
        	$this->redirect();
        } else {
            //consider cids
            $model->setCheckOut();
        }
    }

}
