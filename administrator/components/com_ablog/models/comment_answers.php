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
require_once JPATH_COMPONENT . '/helpers/ablog.php';
jimport('joomla.application.component.model');

class CpanelModelComment_Answers extends JModelLegacy {

    private $total_comment_answers;
    private $search_array = array();

    function getCommentAnswers() {
        $params = JComponentHelper::getParams('com_ablog');
        $ordering = $this->createOrdering($params);
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = 'SELECT * FROM' . $table . $ordering;
        $db->setQuery($query);
        $this->_comment_answers = $db->loadObjectList();
        return $this->_comment_answers;
    }

    function getTotalCommentAnswers() {
        return $this->total_comment_answers;
    }

    function setTotalCommentAnswers($query){
    	$db = JFactory::getDbo();
    	if($db->setQuery($query)){
    		$this->total_comment_answers = count($db->loadObjectList());
    	}    	
    }

    function getCommentAnswer($id) {
        $params = JComponentHelper::getParams('com_ablog');
        $ordering = $this->createOrdering($params);
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = 'SELECT * FROM' . $table . 'WHERE id=' . (int) $id;
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function getCommentAnswersTeaser() {
        return $this->searchForCommentAnswers();
    }

    function delete($cids) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = "DELETE FROM #__ablog_comment_answers
                      WHERE #__ablog_comment_answers.id " .
                'IN(' . implode(',', $cids) . ')';
        $db->setQuery($query);
        if (!$db->execute()) {
            $errorMessage = $this->getDbo()->getErrorMsg();
            throw new Exception($errorMessage);
        }
    }

    function publish() {
        $user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_ablog');
        $table = $this->getTable('comment_answer');
        $cid = JRequest::getVar('cid', '', 'post', 'array');
        $table->publish($cid, 1, $user->id);
    }

    function unpublish() {
        $user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_ablog');
        $table = $this->getTable('comment_answer');
        $cid = JRequest::getVar('cid', '', 'post', 'array');
        $table->publish($cid, 0, $user->id);
    }

    function setTheStateFields() {
        $app = JFactory::getApplication();
        $option = 'com_ablog';
        $search = $app->getUserStateFromRequest($option . 'filter_search_comment_answers', 'filter_search_comment_answers', '', 'string');
        $this->setState('filter.search', $search);
        $filter_state = $app->getUserStateFromRequest($option . 'filter_state', 'filter_state', '', 'cmd');
        $this->setState('filter.state', $filter_state);
        $filter_creator = $app->getUserStateFromRequest($option . 'filter_creator', 'filter_creator', '', 'cmd');
        $this->setState('filter.creator', $filter_creator);
        $filter_date = $app->getUserStateFromRequest($option . 'filter_date', 'filter_date', '', 'cmd');
        $this->setState('filter.date', $filter_date);
        $filter_post_id = $app->getUserStateFromRequest($option . 'filter_post_id', 'filter_post_id', '', 'cmd');
        $this->setState('filter.post.id', $filter_post_id);
        $filter_comment_id = $app->getUserStateFromRequest($option . 'filter_comment_id', 'filter_comment_id', '', 'cmd');
        $this->setState('filter.comment.id', $filter_comment_id);
    }
    

    //triggered in getCommentAnswersTeaser above
    function searchForCommentAnswers() {
        $db = $this->getDbo();
//set the state fields
        $this->setTheStateFields();
        $table = $db->quoteName('#__ablog_comment_answers');
//set the state fields      
        $search_word = $this->getState('filter.search');
        $search_state = $this->getState('filter.state');
        $search_creator = $this->getState('filter.creator');

        $search_post_id = $this->getState('filter.post.id');
 
        $search_comment_id = $this->getState('filter.comment.id');
        $search_date = $this->getState('filter.date');
//the search_(value) is the jrequested int value from the selected field
        $params = JComponentHelper::getParams('com_ablog');
//count rows
        $limit = aBlogHelper::getLimitCommentAnswers();
        $ordering = $this->createOrdering($params);
        $limitstarter = aBlogHelper::getLimitStarterCommentAnswers();
//can search
        $query_columns = 'SELECT * FROM ' . $table;
        $author = null;
        $date = null;
        $post_id = null;
        $data = null;
        $comment_id = null;
    
        
        if ($search_post_id !== '') {
            $data = $this->getDataForView('post_id');
            if(!empty($data)){
                $post_id = (int)$data[$search_post_id]->post_id;
            }
        }

        if ($search_creator !== '') {
            $data = $this->getDataForView('creator');
            if(!empty($data)){
                $author = $db->escape($data[$search_creator]->creator);
            }
        }

        if ($search_date !== '') {
            $data = $this->getDataForView('created_date');
            if(!empty($data)){
                $date = $db->escape($data[$search_date]->created_date);
            }
        }
 
        if($search_comment_id !== ''){
            $data = $this->getDataForView('comment_id');
            if(!empty($data)){
                $comment_id = (int)$data[$search_comment_id]->comment_id;
            }
        }
        //make array mit keys and values for query string
        if ($search_state !== '' && $search_state != 3) {
            $this->search_array[] = array("published" => "$search_state");
        }

        if($search_comment_id !== ''){
            $this->search_array[] = array("comment_id" => "$comment_id");
        }
        

        if ($search_word != '') {
            $this->search_array[] = array("content" => "$search_word");
        }

        if ($search_post_id !== '') {
            $this->search_array[] = array("post_id" => "$post_id");
        }

        if ($search_date != '') {
            $this->search_array[] = array("created_date" => "$date");
        }

        if ($search_creator != '') {
            $this->search_array[] = array("creator" => "$author");
        }
        //for columns_line string
        $column_line = null;
        
        //create columns line for several cases
        if (count($this->search_array) > 1) {

            foreach ($this->search_array as $key => $value) {
                
                if ($key == 0) {
                    foreach ($value as $key => $val) {
                        if (is_string($val)) {
                            $val = $db->quote($val);
                        }
                        $column_line = " WHERE " . $key . "=" . $val;
                    }
                    $query = $query_columns . $column_line;
                } else {
                    foreach ($value as $key => $val) {
                        if (is_string($val)) {
                            $val = $db->quote($val);
                        }
                        $column_line = " AND " . $key . "=" . $val;
                        $query .= $column_line;
                     
                    }
                }
            }
            //if array more than 1 search_fields
        } else {
            //if one case appears
            if ($search_state !== '' && $search_state != 3) {
                $column_line = " WHERE published=" . (int) $search_state;
            }

            if ($search_creator != '') {
                $column_line = " WHERE creator=" . $db->quote($author);
            }
            
            
            if($search_comment_id != ''){
                $column_line = " WHERE comment_id=" . (int)$comment_id;
            }


            if ($search_date != '') {
                $column_line = "  WHERE created_date=" . $db->quote($date);
            }

            if ($search_post_id != '') {
                $column_line = " WHERE post_id=" . (int) $post_id;
            }

            if ($search_word !== '') {
                $search_word = "%" . $db->escape($search_word) . "%";
                $column_line = "WHERE `content` LIKE " . $db->quote($search_word).
                               " OR `email_adress` LIKE " . $db->quote($search_word).
                               " OR `creator` LIKE " . $db->quote($search_word);
            }

            $query = $query_columns . $column_line . $ordering;
        }
	
         $this->setTotalCommentAnswers($query);
         
    
        if (
                $this->getTotalCommentAnswers() != 0 && $search_word != '' && !$this->checkDataViewExistence() ||
                $this->getTotalCommentAnswers() == 0 && $search_word != '' ||
                $this->getTotalCommentAnswers() != 0 && $search_state != '' && !$this->checkDataViewExistence() || 
                $this->getTotalCommentAnswers() != 0 && $search_creator != '' && !$this->checkDataViewExistence() || 
                $this->getTotalCommentAnswers() != 0 && $search_post_id != '' && !$this->checkDataViewExistence() || 
                $this->getTotalCommentAnswers() != 0 && $search_date != '' && !$this->checkDataViewExistence() || 
                $this->getTotalCommentAnswers() != 0 && $search_comment_id != '' && !$this->checkDataViewExistence()
        )
       
       $query = $query;
        $db->setQuery($query, $limitstarter, $limit);
        $results = $db->loadObjectList();
        if($search_word != '' && $results == null){
        	JFactory::getApplication()->enqueueMessage("Search Word not found, please try the first letters or another one.","message");
        }	
       
        return $results;
        
    }

    //get the Data from the Database for the select fields
    function getDataForSelectFields() {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = "SELECT * FROM" . $table;
        $db->setQuery($query);
        $results = $db->loadAssocList();

        return $results;
    }

    //this function takes the data from the selected column in the select fields ordered for the view
    function getDataForView($column) {
        $db = $this->getDBO();
        $params = JComponentHelper::getParams('com_ablog');
        $order = $this->createOrdering($params);
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = "SELECT * FROM " . $table . ' GROUP BY ' . $column . $order;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function checkDataViewExistence(){
        
            if(current($this->getDataForView('post_id'))){
                return true;
            }
            
            if(current($this->getDataForView('creator'))){
                return true;
            }

            if(current($this->getDataForView('created_date'))){
                return true;
            }
            
            if(current($data = $this->getDataForView('comment_id'))){
                return true;
            }
    }

    function updateCommentAnswer() {
        // Get the table
        JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
        $app = JFactory::getApplication();
        $date = JFactory::getDate();
        $row = $this->getTable('comment_answer');
        $input = JFactory::getApplication()->input;
        $cid = $app->input->get('cid','','array');
        $data['creator'] = $app->input->get('creator','','string');
        $data['id'] = $cid[0];
        $data['content'] = $app->input->get('content','','raw');
        $data['published'] = $app->input->get('published','','int');
        $data['created_date'] = $date->toSql();
        // Daten an die Tabelle binden
        $row->reset();
        $id = $data['id'];
        $row->set('id', $id);
        if (!$row->bind($data)) {
            $error = 'Data not bind';
            $this->setError($error);
            return false;
        }
        
        if (!$row->store()) {
            $error = 'Data not stored';
            $this->setError($error);
            return false;
        }
        return true;
    }

    function getTotalPosts() {
        $id = JFactory::getApplication()->input->getVar('id', 0);
        if ($id != '') {
            $db = $this->getDBO();
            $table = $db->quoteName('#__ablog_posts');
            $query = 'SELECT * FROM' . $table .
                    ' WHERE categorie_id=' . (int) $id . ' AND trashed=0';
            $db->setQuery($query);
            $results = $db->loadObjectList();
            return count($results);
        } else {
            $db = $this->getDBO();
            $table = $db->quoteName('#__ablog_posts');
            $query = 'SELECT * FROM' . $table . ' WHERE trashed=0';
            $db->setQuery($query);
            $results = $db->loadObjectList();
            return count($results);
        }
    }

    function createOrdering($params) {
        $ordering = $params->get('ablog_backend_ordering_comment_answers');
        $result = ' ORDER BY id ASC';
        if ($ordering) {
            $result = ' ORDER BY id DESC';
        }
        return $result;
    }

    function getCheckouts() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_comment_answers');
        $cids = JFactory::getApplication()->input->get('cid', '', '');
        if (is_array($cids)) {
            $cids = $cids[0];
        }
        $query = 'SELECT checked_out,checked_out_time FROM ' . $table . ' WHERE id IN(' . (int) $cids . ')';
        $db->setQuery($query);
        return $db->loadObject();
    }

    function checkIsSameUserAndFieldsFull() {
        $checkouts = $this->getCheckOuts();
        $user = JFactory::getUser();
        $user_id = $user->get('id');

        if ($checkouts->checked_out == $user_id && $checkouts->checked_out_time > 0) {
            return 1;
        } else if ($checkouts->checked_out == 0 || $checkouts->checked_out == $user_id && !$checkouts->checked_out_time) {
            return 0;
        }
    }

    function checkIsNotSameUser() {
        $checkouts = $this->getCheckOuts();
        $user = JFactory::getUser();
        $user_id = $user->get('id');

        if ($checkouts->checked_out != $user_id) {
            return 1;
        }
    }

    function setCheckOut() {
        $db = $this->getDbo();
        $user = JFactory::getUser();
        $date = JFactory::getDate();
        $timedate = $db->quote($date->toSql());
        $user_id = $db->quote($user->get('id'));
        $table = $db->quoteName('#__ablog_comment_answers');
        $cids = JFactory::getApplication()->input->getVar('cid', '', '');
        if(count($cids) == 1) {
            if (is_array($cids)) {
                $cids = $cids[0];
            }
            $query = 'UPDATE ' . $table . 'SET checked_out=' . $user_id . ',checked_out_time=' . $timedate . ' WHERE id IN(' . (int) $cids . ')';
            $db->setQuery($query);
            $db->execute();
        }
    }

    function checkIn() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_comment_answers');
        $cids = JFactory::getApplication()->input->get('cid', '', 'array');
        $query = "UPDATE " . $table . " SET checked_out='null',checked_out_time='null' WHERE id IN(" . implode(',', $cids) . ")";
        $db->setQuery($query);
        $db->execute();
    }
}
?>
