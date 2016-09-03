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
jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/ablog.php';

class CpanelControllerComment_answers extends JControllerLegacy {

    //Get CommentAnswers View and set the Model
    function display($cachable = false, $urlparams = false) {
        $view = $this->getView('comment_answers', 'html');
        $model = $this->getModel('comment_answers');
        $view->setModel($model);
        $view->display();
    }

    function publish() {
        $model = $this->getModel('comment_answers');
        $model->publish();
        $this->setRedirect('index.php?option=com_ablog&act=comment_answers');
    }

    function unpublish() {
        $model = $this->getModel('comment_answers');
        $model->unpublish();
        $this->setRedirect('index.php?option=com_ablog&act=comment_answers');
    }

    function remove() {
        JSession::checkToken();
        $model = $this->getModel('comment_answers');
        $cids = JFactory::getApplication()->input->get('cid', '', 'array');
        $model->delete($cids);
        $this->setRedirect('index.php?option=com_ablog&act=comment_answers');
    }

    //Get the CommentAnswers that belong to the defined post and comment
    function getCommentAnswers($post_id, $comment_id) {
        $db = JFactory::getDBO();
        $table = $db->nameQuote('#__ablog_comment_answers');
        $query = 'SELECT * FROM' . $table .
                'WHERE post_id=' . (int)$post_id . ' AND comment_id=' . (int)$comment_id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function edit() {        
        JSession::checkToken() or jexit('INVALID TOKEN');
        $model = $this->getModel('comment_answers');
        $this->handleEditCheckouts($model);
        $view = $this->getView('comment_answer', 'html');
        $id = JFactory::getApplication()->input->get('cid','','');
        if (is_array($id) && count($id) > 1){
        	$this->setRedirect('index.php?option=com_ablog&act=comment_answers');
        	JFactory::getApplication()->enqueueMessage('Please choose only one row for edit','notice');
        }
        $results = $model->getCommentAnswer($id[0]);
        $view->assignRef('results', $results);
        $view->display();
    }
    
   private function handleEditCheckouts($model) {
        //set CheckOut Table if not empty
        if (!$model->checkIsSameUserAndFieldsFull()) {
            $model->setCheckOut();
        } else if ($model->checkIsNotSameUser()) {
            //consider cids
            $url = 'index.php?option=com_ablog&act=comment_answers';
            $this->setRedirect($url, 'Check-out failed with the following error: The user checking out does not match the user who checked out the item. ');
            return $this->redirect();
        }
    }
    
    public function checkIn() {
        $model = $this->getModel('comment_answers');
        $model->checkIn();
        $this->setRedirect('index.php?option=com_ablog&act=comment_answers');
    }
}