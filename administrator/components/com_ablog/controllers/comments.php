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

class CpanelControllerComments extends JControllerLegacy {

    function display($cachable = false, $urlparams = false) {
        //start the view and inject the model inkl.limits
        $view = $this->getView('comments', 'html');
        $model = $this->getModel('comments');
        $view->setModel($model);
        $view->display();
    }

    function publish() {
        $model = $this->getModel('comments');
        $model->publish();
        $this->setRedirect('index.php?option=com_ablog&act=comments');
    }

    function unpublish() {
        $model = $this->getModel('comments');
        $model->unpublish();
        $this->setRedirect('index.php?option=com_ablog&act=comments');
    }

    function remove() {
        $model_comments = & $this->getModel('comments');
        $cids = JFactory::getApplication()->input->getVar('cid', '', 'post', 'array');
        $model_comments->delete($cids);
        $this->setRedirect('index.php?option=com_ablog&act=comments');
    }

    function edit() {        
        $model = $this->getModel('comments');
        $this->handleEditCheckOut($model);
        $view = $this->getView('comment', 'html');
        $id = JFactory::getApplication()->input->get('cid','','');
        
        if (is_array($id) && count($id) > 1){
        	$this->setRedirect('index.php?option=com_ablog&act=comments');
        	JFactory::getApplication()->enqueueMessage('Please choose only one row for edit','notice');
        }
        $results = $model->getComment($id[0]);
        $view->assignRef('results', $results);
        $view->display();
    }
    
     private function handleEditCheckOut($model) {
        //set CheckOut Table if not empty
        if (!$model->checkIsSameUserAndFieldsFull()) {
            $model->setCheckOut();

        } else if ($model->checkIsNotSameUser()) {
            //consider cids   
            $url = 'index.php?option=com_ablog&act=blog_categories';
            $this->setRedirect($url, 'Check-out failed with the following error: The user checking out does not match the user who checked out the item. ');
            return $this->redirect();
        }
    }
    
    public function checkIn() {
        $model = $this->getModel('comments');
        $model->checkIn();
        $this->setRedirect('index.php?option=com_ablog&act=comments');
    }
}