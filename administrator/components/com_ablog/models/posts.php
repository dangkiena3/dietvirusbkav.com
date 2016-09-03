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
jimport('joomla.utilities.date');
require_once JPATH_COMPONENT . '/helpers/ablog.php';

class CpanelModelPosts extends JModelLegacy {
	

    public $total_posts_query;

    function getPosts() {    
        $params = JComponentHelper::getParams('com_ablog');
        $ordering = $this->createOrdering($params);
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $query = 'SELECT * FROM' . $table . ' ORDER BY' . $ordering;
        $db->setQuery($query);
        $this->_posts = $db->loadObjectList();
       
        return $this->_posts;
    }

    function getPostsTeaser() {
        $db = $this->getDBO();
        
        $table = $db->quoteName('#__ablog_posts');
        $query = $this->searchForPosts($table);
    
        $app = JFactory::getApplication();
        $limitstarter = aBlogHelper::getLimitStarterPosts();
        $limit = aBlogHelper::getLimitPosts();
        if($app->input->get('filter_state') == 3){
            $limitstarter = null;
            $limit = null;
        }
     
        $db->setQuery($query,$limitstarter,$limit);
        $this->_posts = $db->loadObjectList();
       //scheduling posts
        $this->schedulingPosts($this->_posts);
        $option = 'com_ablog';       
      
        $search = $app->getUserStateFromRequest($option . 'filter_search_post', 'filter_search_post');
        $state = $app->getUserStateFromRequest($option . 'filter_state', 'filter_state');
        $this->searchWordMessage($this->_posts,$search);
        return $this->_posts;
    }
    
    function searchWordMessage($posts_result,$search){
        if(!empty($posts_result) && $search != ''){
            $app = JFactory::getApplication();
            $app->enqueueMessage("The Search word wasn´t found, please try another one!", 'message');
        }
    }

    function getPost($cid) {
        $params = JComponentHelper::getParams('com_ablog');
        $ordering = $this->createOrdering($params);
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $query = 'SELECT * FROM' . $table . 'WHERE id=' . (int) $cid;
        $db->setQuery($query);
        $this->_post = $db->loadObject();
        return $this->_post;
    }

    function delete($cids) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $query = 'DELETE FROM' . $table .
                'WHERE id ' .
                'IN(' . implode(',', $cids) . ')';
        $db->setQuery($query);
        return $db->execute();
    }

    function storeEdit() {
        $app = JFactory::getApplication();
        $row = $this->getTable('Post');
        //editEditor
        $data = $app->input->getArray(
                array(
                    'title' => 'string',
                    'categorie_id' => 'int',
                    'creator_username' => 'string',
                    'creator' => 'string',
                    'content' => 'raw',
                    'publish_start' => 'string',
                    'publish_stop' => 'string',
                    'published' => 'int',
                    'hits' => 'int',
                    'creator_id' => 'int',
                    'post_id' => 'int',
                ),$_POST);
        $data['content'] = aBlogHelper::filterText($data['content']);
        
        $date = JFactory::getDate();
        $data['created_date'] = $date->toSql();
        $row->reset();
        
        $row->set('id', $data['post_id']);
        if (!$row->bind($data)) {
            $error = 'Data not bind';
            throw new Exception('post data not binded');
        }

        if (!$row->check()) {
            $error = 'Data not checked';
            throw new Exception('post data checked failed');
            return false;
        }

        if (!$row->store()) {
            $error = 'Data not stored';
            throw new Exception('post data not stored');
            return false;
        }
        return true;
    }

    function publish() {
        $user = JFactory::getUser();
        $row = $this->getTable('post');
        $app = JFactory::getApplication();
        $cid = $app->input->getVar('cid', '', 'post');
        $row->publish($cid, 1, $user->id);
        //Call the untrash method
        $this->untrash();
    }

    function unpublish() {
        $user = JFactory::getUser();
        $row = $this->getTable('post');
        $app = JFactory::getApplication();
        $cid = $app->input->getVar('cid', '', 'post');
        $row->publish($cid, 0, $user->id);
    }

    function setTheStateFields() {
        $app = JFactory::getApplication();
        $option = 'com_ablog';
        $search_word = $app->getUserStateFromRequest($option . 'filter_search_posts', 'filter_search_posts', '', 'string');
        $this->setState('filter.search_posts', $search_word);
        $state = $app->getUserStateFromRequest($option . 'filter_state', 'filter_state', '', 'int');
        $this->setState('filter.state', $state);
    }

    function searchForPosts($table) {
        $app = JFactory::getApplication();
        //set the state fields for further operations
        $db = JFactory::getDbo();
        $this->setTheStateFields();
        //get the input fields values to decide which query to use
        $search_word = $this->getState('filter.search_posts');
        $search_state = $this->getState('filter.state');
        $params = JComponentHelper::getParams('com_ablog');
        //limit section     
        $ordering = $this->createOrdering($params);

        if ($search_word != '' && $search_state == -2) {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time,published ,hits ,creator, creator_id,creator_username,creator_id,categorie_id, trashed, publish_start,publish_stop  FROM " . $table . " WHERE title LIKE " . $db->quote('%'.$search_word.'%') . " AND trashed=1 OR content LIKE ".$db->quote('%'.$search_word.'%')." AND trashed=1 " . $ordering;
            return $query;
        }
        
     	if ($search_word != '' && $search_state == 1) {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time,published ,hits ,creator, creator_id,creator_username,creator_id,categorie_id, trashed, publish_start,publish_stop  FROM " . $table . " WHERE title LIKE " . $db->quote('%'.$search_word.'%') . " AND trashed=0 AND published=1 OR content LIKE ".$db->quote('%'.$search_word.'%')." AND trashed=0 AND published=1" . $ordering;
           return $query;
        }
        
        if ($search_word != '' && $search_state == 4) { 
        	$query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time,published ,hits ,creator, creator_id,creator_username,creator_id,categorie_id, trashed, publish_start,publish_stop  FROM " . $table . " WHERE title LIKE " . $db->quote('%'.$search_word.'%') . " AND trashed=0 AND published=0 OR content LIKE ".$db->quote('%'.$search_word.'%')." AND published=0" . $ordering;
        	 return $query;
        }
        
        if ($search_word != '' && $search_state == 3) {
        	$query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time,published ,hits ,creator, creator_id,creator_username,creator_id,categorie_id, trashed, publish_start,publish_stop  FROM " . $table . " WHERE title LIKE " . $db->quote('%'.$search_word.'%') . " OR content LIKE ".$db->quote('%'.$search_word.'%') . $ordering;
        	return $query;
        } 
        
        if ($search_word != '' && $search_state == '') {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time,published ,hits ,creator, creator_id,creator_username,creator_id,categorie_id, trashed, publish_start,publish_stop  FROM " . $table . " WHERE title LIKE " . $db->quote('%'.$search_word.'%') . " AND trashed=0 	 	OR content LIKE ".$db->quote('%'.$search_word .'%')."AND trashed=0 " . $ordering;
            return $query;
        }
        
        if ($search_state == '' && $search_word == '') {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out ,checked_out_time ,published ,hits ,creator,creator_id,creator_username, creator_id, categorie_id, trashed,publish_start,publish_stop  FROM " . $table . " WHERE trashed=0" . $ordering;
            return $query;
        }
        
        if ($search_state == 1 && $search_word == '') {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time , published ,hits ,creator ,creator_id,creator_username, categorie_id, trashed, publish_start, publish_stop  FROM " . $table . " WHERE published ='1' AND trashed='0'" . $ordering;
            return $query;
        }
        
        if ($search_state == 4 || $search_state == 0) {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out,checked_out_time,published ,hits ,creator,creator_id, creator_username, categorie_id, trashed, publish_start, publish_stop  FROM " . $table . " WHERE published='0'" . $ordering;
            return $query;
        }

        if ($search_state == -2 && $search_word == '') {
            $query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out ,checked_out_time ,published ,hits ,creator ,creator_id,creator_username ,categorie_id, trashed, publish_start, publish_stop  FROM " . $table . " WHERE trashed='1'" . $ordering;
            return $query;
        }
        
        if ($search_state == 0 && $search_word == '') {
        	$query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out ,checked_out_time ,published ,hits ,creator ,creator_id,creator_username ,categorie_id, trashed, publish_start, publish_stop  FROM " . $table . " WHERE trashed='0'" . $ordering;
        	return $query;
        }
        
        if ($search_state == 1 && $search_word == '') {
        	$query = "SELECT id,created_date ,title ,SUBSTR(content,1,50) as content ,checked_out ,checked_out_time ,published ,hits ,creator ,creator_id,creator_username ,categorie_id, trashed, publish_start, publish_stop  FROM " . $table . " WHERE trashed='0'" . $ordering;
        	return $query;
        }
        
        if ($search_state == 3 && $search_word == '') {
            $query = "SELECT * FROM " . $table . $ordering;
            return $query;
        }
    }

    private function getMessage($msg) {
        JFactory::getApplication()->enqueueMessage($msg);
    }

    function setTrash() {
        JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $cids = JRequest::getVar('cid');
        $db->setQuery(
                'UPDATE' . $table .
                ' SET trashed = 1 ' .
                ' WHERE id IN (' . implode(',', $cids) . ')'
        );
        return $db->execute();
    }

    function untrash() {
        JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $cids = JRequest::getVar('cid');
        $db->setQuery(
                'UPDATE' . $table .
                ' SET trashed = 0 ' .
                ' WHERE id IN (' . implode(',', $cids) . ')'
        );
        $db->query();
    }

    function getLastInsertedPostId() {
        $db = $this->getDBO();
        $db->quoteName('#__ablog_posts');
        return $db->insertid();
    }

    function getTotalPosts() {
    	$db = $this->getDBO();
    	$table = $db->quoteName('#__ablog_posts');
    	$query = 'SELECT * FROM' . $table;
    	$db->setQuery($query);
    	$this->_posts = $db->loadObjectList();
    	return count($this->_posts);
    }

    function createOrdering($params) {
        $ordering = $params->get('ablog_backend_ordering_posts');
        $result = ' ORDER BY id ASC';
        if ($ordering) {
            $result = ' ORDER BY id DESC';
        }
        return $result;
    }

    private function schedulingPosts($allposts) {
        $db = JFactory::getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $id_quoted = $db->quoteName('id');
        $published_quote = $db->quoteName('published');

        if ($allposts) {
            $current_date = JFactory::getDate(null, JFactory::getConfig()->get('offset'));
            $current_date = $current_date->__toString();

            foreach ($allposts as $post) {

                if ($post->publish_start <= $current_date && $post->publish_stop > $current_date ) {
                    $db->setQuery('UPDATE ' . $table . ' SET ' . $published_quote .'= 1 WHERE'. $id_quoted . '=' . $post->id);
                    $db->execute();
                }
                if ($post->publish_stop <= $current_date && $post->publish_stop != $post->publish_start) {
                     $db->setQuery('UPDATE ' . $table . ' SET ' . $published_quote .'= 0 WHERE'. $id_quoted . '=' . $post->id);
                    $db->execute();
                }
            }
            return;
        }
    }   
   

    function getCheckOut() {
        $db = $this->getDbo();
        $cid = $this->getCid();
        $table = $db->quoteName('#__ablog_posts');
        $query = "SELECT checked_out,checked_out_time FROM " . $table . " WHERE id=" . (int) $cid;
        $db->setQuery($query);
        return $db->loadObject();
    }

   
    function checkOutIsSameUserOrZero() {
        $checked_out = $this->getCheckOut();
        $user = JFactory::getUser();
        $user_id = $user->get('id');
        return $checked_out->checked_out == $user_id || $checked_out->checked_out == 0;        
    }
    
    
    function getCid(){
    	$cid = JFactory::getApplication()->input->get('cid', '', '');
    	if(is_array($cid) && count($cid) == 1 && (int)$cid[0]){
    		return $cid[0];
    	}
    	else if(is_string($cid) && ctype_digit($cid)){
    		return $cid;
    	}
    }


    function setCheckOut() {
        $db = $this->getDbo();
        $cid = $this->getCid();
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $timedate = $db->quote($date->toSql());
        $user_id = $user->get('id');
        $table = $db->quoteName('#__ablog_posts');
        $query = "UPDATE " . $table . " SET checked_out=" . (int) $user_id . ",checked_out_time=" . $timedate . " WHERE id=" . (int) $cid;
        $db->setQuery($query);
        $db->execute();
    }

    function checkIn() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $cid = $this->getCid();
        $query = "UPDATE " . $table . "SET checked_out='null',checked_out_time='null' WHERE id=" . (int) $cid;
        $db->setQuery($query);
        return $db->execute();
    }
}
