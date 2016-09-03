
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
jimport('joomla.application.component.model');

class CpanelModelComments extends JModelList {

    private $total_comments;
    private $search_array = array();

    function getComments() {
        $params = JComponentHelper::getParams('com_ablog');
        $order = $this->createOrdering($params);
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comments');
        $query = 'SELECT * FROM' . $table . $order;
        $db->setQuery($query);
        $this->_comments = $db->loadObjectList();
        return $this->_comments;
    }

    function getTotalComments() {
        return $this->total_comments;
    }

    function setTotalComments($query) {
        $db = JFactory::getDbo();
 
        if ($db->setQuery($query)) {
            $this->total_comments = count($db->loadObjectList());
        }
    }

    function getCommentsTeaser(){        
        return $this->searchForComments();
    }

    function delete($cids) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comments');
        $id = $db->quoteName('id');
        $query = "DELETE FROM #__ablog_comments
                      WHERE #__ablog_comments.id " .
                'IN(' . implode(',', $cids) . ')';
        $db->setQuery($query);
        if (!$db->query()) {
            $errorMessage = $this->getDBO()->getErrorMsg();
            JError::raiseError(500, 'Error deleting comments ' . $errorMessage);
            return false;
        }
        $table = $db->quoteName('#__ablog_comment_answers');
        $comment_id = $db->quoteName('comment_id');
        $query = 'DELETE FROM' . $table .
                ' WHERE' . $comment_id .
                'IN(' . implode(',', $cids) . ')';
        $db->setQuery($query);
        if (!$db->query()) {
            $errorMessage = $this->getDBO()->getErrorMsg();
            JError::raiseError(500, 'Error deleting comment answers ' . $errorMessage);
            return false;
        }
    }

    function publish() {
        $user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_ablog');
        $table = $this->getTable('comment');
        $cid = JRequest::getVar('cid', '', 'post', 'array');
        $table->publish($cid, 1, $user->id);
    }

    function unpublish() {
        $user = JFactory::getUser();
        $table = $this->getTable('comment');
        $cid = JRequest::getVar('cid', '', 'post', 'array');
        $table->publish($cid, 0, $user->id);
    }

    function setTheStateFields() {
        $option = 'com_ablog';
        $search_word = $this->getUserStateFromRequest($option . 'filter_search_comments', 'filter_search_comments', '', 'string');
        $this->setState('filter.search', $search_word);
        $filter_state = $this->getUserStateFromRequest($option . 'filter_state', 'filter_state', '', 'cmd');
        $this->setState('filter.state', $filter_state);
        $filter_creator = $this->getUserStateFromRequest($option . 'filter_creator', 'filter_creator', '', 'cmd');
        $this->setState('filter.creator', $filter_creator);
        $filter_post_id = $this->getUserStateFromRequest($option . 'filter_post_id', 'filter_post_id', '', 'cmd');
        $this->setState('filter.post_id', $filter_post_id);
        $filter_date = $this->getUserStateFromRequest($option . 'filter_date', 'filter_date', '', 'cmd');
        $this->setState('filter.date', $filter_date);
    }

//return the data for the view depends on the filter state
    function searchForComments() {
        $db = $this->getDbo();
//set the state fields
        $this->setTheStateFields();
        $table = $db->quoteName('#__ablog_comments');
//set the state fields      
        $search_word = $this->getState('filter.search');
        $search_state = $this->getState('filter.state');
        $search_creator = $this->getState('filter.creator');

        $search_post_id = $this->getState('filter.post_id');
        $search_date = $this->getState('filter.date');
//the search_(value) is the jrequested int value from the selected field
        $params = JComponentHelper::getParams('com_ablog');
//count rows
        $limit = aBlogHelper::getLimitComments();
        $order = $this->createOrdering($params);
        $limitstarter = aBlogHelper::getLimitStarterComments();
//can search
        $query_columns = 'SELECT * FROM ' . $table;
        $author = null;
        $date = null;
        $post_id = null;
        $data = null;

        if ($search_post_id !== '') {
            $data = $this->getDataForView('post_id');
                if(!empty($data)){
                    $post_id = $db->escape($data[$search_post_id]->post_id);
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

        //make array with keys and values for query string
        if ($search_state !== '' && $search_state != 3) {
            $this->search_array[] = array("published" => "$search_state");
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
                        if(is_string($val)){$val = $db->quote($val);}
                        $column_line = " WHERE " . $key . "=" . $val;
                    }
                    $query = $query_columns . $column_line;
                } else {
                    foreach ($value as $key => $val) {
                        if(is_string($val)){$val = $db->quote($val);}
                        $column_line = " AND " . $key . "=" . $val;
                        $query .=  $column_line;
                    }
                }
            }
            //if array more than 1 search_fields
        } else {
            //if one case appears
            if ($search_state !== '' && $search_state != 3) {
                    //$search_state = 0;
                $column_line = " WHERE published=" . (int) $search_state;
            }            

            if ($search_creator != '') {
                $column_line = " WHERE creator=" . $db->quote($author);
            }


            if ($search_date != '') {
                $column_line = "  WHERE created_date=" . $db->quote($date);
            }

            if ($search_post_id !== '') {
                $column_line = " WHERE post_id=" . (int) $post_id;
            }

            if ($search_word != '') {
                $search_word = "%" . $db->escape($search_word) . "%";
                $column_line = "WHERE `creator` LIKE " . 
                $db->quote($search_word) . 
                " OR `content` LIKE " . 
                $db->quote($search_word).
                " OR `email_adress` LIKE " .
                $db->quote($search_word);
            }

            $query = $query_columns . $column_line;
        }

        $this->setTotalComments($query);
        
        
          if ($this->getTotalComments() != 0 && $search_word != '' && !$this->checkDataViewExistence()  || 
              $this->getTotalComments() != 0 && $search_state != ''  && $this->checkDataViewExistence() ||
              $this->getTotalComments() != 0 && $search_creator != '' && $this->checkDataViewExistence() || 
              $this->getTotalComments() != 0 && $search_post_id != '' && $this->checkDataViewExistence() ||
              $this->getTotalComments() != 0 && $search_date != ''
          ) 
          
          $query = $query . $order;
          $db->setQuery($query, $limitstarter, $limit);
          $results = $db->loadObjectList();
          if($search_word != '' && $results == null){
              JFactory::getApplication()->enqueueMessage("Search Word not found, please try the first letters or another one.","message");
          }
          return $results;
        
    }

//select the data for the filter fields
    function getDataForView($column) {
        $params = JComponentHelper::getParams('com_ablog');
        $order = $this->createOrdering($params);
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comments');
        $query = "SELECT * FROM " . $table . " GROUP BY " . $column . $order;
        $db->setQuery($query);

        $results = $db->loadObjectList();

        return $results;
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
            
            if(current($this->getDataForView('email_adress'))){
                return true;
            }
            
            if(current($this->getDataForView('content'))){
                return true;
            }          
            
    }

    function updateComments() {
        JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
        $date = JFactory::getDate();
        $row = $this->getTable('comment');
        $input = JFactory::getApplication()->input;
        $data = $input->getArray($_POST);
        $data['created_date'] = $date->toSql();
// Daten an die Tabelle binden
        $row->reset();
        $id = $data['id'];
        $row->set('id', $id);
        if (!$row->bind($data)) {
            $error = 'Data not bind';
            throw new Exception($error);
        }
        if (!$row->store()) {
            $error = 'Data not stored';
            throw new Exception($error);
        }
        return true;
    }

    function getComment($id) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comments');
        $query = 'SELECT * FROM' . $table . 'WHERE id=' . (int) $id;
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function createOrdering($params) {
        $ordering = $params->get('ablog_backend_ordering_comments');
        $result = ' ORDER BY id ASC';
        if ($ordering) {
            $result = ' ORDER BY id DESC';
        }
        return $result;
    }

    function getCheckouts() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_comments');
        $cids = JFactory::getApplication()->input->getVar('cid', '', 'post', 'array');
        $query = 'SELECT checked_out,checked_out_time FROM ' . $table . ' WHERE id IN(' . implode(',', $cids) . ')';
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
        $table = $db->quoteName('#__ablog_comments');
        $cids = JFactory::getApplication()->input->getVar('cid', '', 'post', 'array');
        if(count($cids) == 1){
            $query = 'UPDATE ' . $table . 'SET checked_out=' . $user_id . ',checked_out_time=' . $timedate . ' WHERE id IN(' . implode(',', $cids) . ')';
                $db->setQuery($query);
            }
        $db->execute();
    }

    function checkIn() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_comments');
        $cids = JFactory::getApplication()->input->get('cid', '', 'array');
        $query = " UPDATE " . $table . " SET checked_out='null',checked_out_time='null' WHERE id IN(" . implode(',', $cids) . ")";
        $db->setQuery($query);
        $db->execute();
    }
}