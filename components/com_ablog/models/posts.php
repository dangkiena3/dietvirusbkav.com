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
defined('_JEXEC') or die('Restricted access');

// Load the base JModel class
/**
 * Revue Model
 */
class ABlogModelPosts extends JModelLegacy {

	
    function getAllIdPosts($categorie_id) {
    	$app = JFactory::getApplication();
        $db = $this->getDBO();
        $params = JComponentHelper::getParams('com_ablog');
        $table = $db->quoteName('#__ablog_posts');
        $query = 'SELECT * FROM' . $table . ' WHERE categorie_id=' . (int) $categorie_id . ' AND trashed=0 AND published=1';
        $limitstarter = aBlogHelper::getPostsLimitStarter();
        $limit = $params->get('ablog_frontend_posts_limit');
        $db->setQuery($query, $limitstarter, $limit);
        $results = $db->loadObjectList();
        return $results;
    }
    
    function getAllIdPostsEdit($categorie_id) {
    	$app = JFactory::getApplication();
    	$db = $this->getDBO();
    	$params = JComponentHelper::getParams('com_ablog');
    	$table = $db->quoteName('#__ablog_posts');
    	$query = 'SELECT * FROM' . $table . ' WHERE categorie_id=' . (int) $categorie_id . ' AND trashed=0 AND published=1';
    	$limitstarter = aBlogHelper::getPostsEditLimitStarter();
    	$limit = aBlogHelper::getPostsEditLimit();
    	$db->setQuery($query, $limitstarter, $limit);
    	$results = $db->loadObjectList();
    	return $results;
    }
    

    function getAllPosts() {
        $this->schedulingPosts($this->getPostsForSchedulingPosts());
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $limitstarter = aBlogHelper::getPostsLimitStarter();
        $params = JComponentHelper::getParams('com_ablog');
        $query = 'SELECT * FROM ' . $table . ' WHERE trashed=0 AND published=1';
        $limit = $params->get('ablog_frontend_posts_limit');
        $db->setQuery($query, $limitstarter, $limit);
        $results = $db->loadObjectList();
        return $results;
    }
    
    function getAllPostsEdit() {
    	$app = JFactory::getApplication();
    	$db = $this->getDBO();
    	$table = $db->quoteName('#__ablog_posts');
    	$query = 'SELECT * FROM ' . $table . ' WHERE trashed=0';
    	$limit = aBlogHelper::getPostsEditLimit();
    	$limitstarter = aBlogHelper::getPostsEditLimitStarter();
    	$db->setQuery($query,$limitstarter,$limit);
    	$results = $db->loadObjectList();
    	return $results;
    }
    
    function getPostsForSchedulingPosts(){
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','published','publish_start','publish_stop','trashed')));
        $table = $db->quoteName('#__ablog_posts');
        $query->from($table);
        $query->where($db->quoteName('trashed').'='.(int)0);       
        $db->setQuery($query);
        return $db->loadObjectList();
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
        
                if ($post->publish_start <= $current_date && $post->publish_stop > $current_date) {
                    $db->setQuery('UPDATE ' . $table . ' SET ' . $published_quote . '= 1 WHERE' . $id_quoted . '=' . (int)$post->id);
                    $db->execute();
                }
                if ($post->publish_stop <= $current_date && $post->publish_stop != $post->publish_start) {   
                    $db->setQuery('UPDATE ' . $table . ' SET ' . $published_quote . '= 0 WHERE' . $id_quoted . '=' . (int)$post->id);
                    $db->execute();
                }
            }
        }
    }

   

    function getPost($id) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $id_name = $db->quoteName('id');
        $published_name = $db->quoteName('published');
        $trashed_name = $db->quoteName('trashed');
        $query = 'SELECT * FROM ' . $table .
                ' WHERE '. $id_name . '=' . (int) $id . ' AND ' . $published_name .'=1 AND '. $trashed_name .'=0';

        $db->setQuery($query);
        $result = $db->loadObject();
        $this->schedulingPost($this->getPostForSchedulingPost($id));
        return $result;
    }
    
    private function getPostForSchedulingPost($id){
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','published','publish_start','publish_stop')));
        $query->from($db->quoteName('#__ablog_posts'));
        $query->where($db->quoteName('id').'='.(int)$id. ' AND ' .$db->quoteName('trashed'). '=' .(int)0);
        $db->setQuery($query);
        return $db->loadObject();
    }


    private function schedulingPost($post) {
        $db = JFactory::getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $id_quoted = $db->quoteName('id');
        $published_quote = $db->quoteName('published');

        if ($post) {
            $current_date = JFactory::getDate(null, JFactory::getConfig()->get('offset'));
            $current_date = $current_date->__toString();
            if ($post->publish_start <= $current_date && $post->publish_stop > $current_date) {
                $db->setQuery('UPDATE ' . $table . ' SET ' . $published_quote . '= 1 WHERE ' . $id_quoted . '=' . (int)$post->id);
                $db->execute();
            }

            if ($post->publish_stop <= $current_date && $post->publish_stop != $post->publish_start) {
                $db->setQuery('UPDATE ' . $table . ' SET ' . $published_quote . '= 0 WHERE ' . $id_quoted . '=' . (int)$post->id);
                $db->execute();
            }
        }
        return;
    }

    function getPostEvenNotPublished($id) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_posts');
        $query = 'SELECT * FROM ' . $table .
                ' WHERE id=' . (int) $id . ' AND trashed=0';
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    function createPaginationPosts() {
    	$app = JFactory::getApplication();
    	$id = $app->input->getInt('id');
    	$total = $this->getTotalPosts();
    	$limitstarter = aBlogHelper::getPostsLimitStarter();
    	$params = JComponentHelper::getParams('com_ablog');
    	$limit = $params->get('ablog_frontend_posts_limit');
    	$pagination = new JPagination($total, $limitstarter, $limit,'posts');    
    	return $pagination->getPagesLinks();
    }
    
    function createPagination(){
    	$app = JFactory::getApplication();
    	$id = $app->input->getInt('id');
    	$total = $model->getTotalPosts();
    	$params = JComponentHelper::getParams('com_ablog');
    	$limitstarter = aBlogHelper::getPostsLimitStarter();
    	$limit = $params->get('ablog_frontend_posts_limit');
    	$pagination = new JPagination($total, $limitstarter, $limit);
    	return $pagination->getPagesLinks();
    }
    
    function createPaginationEdit(){
    	$app = JFactory::getApplication();    	
    	$total = $this->getTotalPosts();
    	$id = $app->input->getInt('id');
    	$limitstarter = aBlogHelper::getPostsEditLimitStarter();
    	$limit = aBlogHelper::getPostsEditLimit();
    	return new JPagination($total, $limitstarter, $limit,'postsedit');
    }
    
    function getTotalPosts(){
    	$id = JFactory::getApplication()->input->getInt('id', 0);
    	if ($id != '') {
    		$db = $this->getDBO();
    		$table = $db->quoteName('#__ablog_posts');
    		$query = 'SELECT * FROM ' . $table .
    		' WHERE categorie_id=' . (int) $id . ' AND trashed=0';
    		$db->setQuery($query);
    		$results = $db->loadObjectList();
    		return count($results);
    	}else{
            $db = $this->getDBO();
            $table = $db->quoteName('#__ablog_posts');
            $query = 'SELECT * FROM ' . $table . ' WHERE trashed=0';
            $db->setQuery($query);
            $results = $db->loadObjectList();
            return count($results);
    	}
    }

    function updatePostHits() {
        $id = JFactory::getApplication()->input->getCmd('id');
        $db = $this->getDbo();
        $db->setQuery(
                'UPDATE #__ablog_posts' .
                ' SET hits = hits + 1' .
                ' WHERE id = ' . (int) $id
        );
        $db->query();
    }

    public function publish($table, $column_id) {
        $db = JFactory::getDbo();
        $query = 'UPDATE ' . $db->quoteName($table) . 'SET published="1" WHERE id=' . (int) $column_id;
        $db->setQuery($query);
        return $db->execute();
    }

    private function unpublish($table, $column_id) {
        $db = JFactory::getDbo();
        $query = 'UPDATE ' . $db->quoteName($table) . 'SET published="0" WHERE id=' . (int) $column_id;
        $db->setQuery($query);
        $db->execute();
    }

    public function storePostEdit($data) {
        JSession::checkToken() or jexit('INVALID TOKEN');
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $user = JFactory::getUser();
        $date = JFactory::getDate();
        $data['created_date'] = $date->toSql();
        $query = "INSERT INTO" . $table .
                "(id, created_date,title,content,categorie_id,creator,creator_id,published,publish_start,publish_stop) VALUES('', " .
                $db->quote($data['created_date']) . ", " . $db->quote($data['title_edit']) . ", " . $db->quote($data['ablog_front_content']) .
                ", " . $db->quote($data['edit_kategorie']) . ", " . $db->quote($user->name) . ", " . (int) $user->id . ", " . (int) $data['published'] .
                ", " . $db->quote($data['publish_start']) . ", " . $db->quote($data['publish_stop']) . ")";
        $error_num = $db->setQuery($query)->getErrorNum();
        $db->execute();
        return $error_num;
    }

    public function updatePostEdit($data) {
        $db = JFactory::getDbo();
        $date = JFactory::getDate();
        $date = $date->toSql();
        $table = $db->quoteName('#__ablog_posts');
        $query = "UPDATE " . $table . " SET "
                . "created_date=" . $db->quote($date)
                . ",title=" . $db->quote($data['title_edit'])
                . ",content=" . $db->quote($data['ablog_front_content'])
                . ",published=" . (int) $data['published']
                . ",publish_start=" . $db->quote($data['publish_start'])
                . ",publish_stop=" . $db->quote($data['publish_stop']) .
                " WHERE id=" . (int) $data['id'];
        $db->setQuery($query);
        return $db->execute();
    }

    public function getLastInsertedPost() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $id = $db->insertid();
        $query = "SELECT * FROM " . $table . " WHERE id=" . (int) $id;
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getPostById($id) {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $query = "SELECT * FROM " . $table . " WHERE id=" . (int) $id;
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function checkedOut() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $app = JFactory::getApplication();
        $date = JFactory::getDate();
        $id = $app->input->get('id', '', 'int');
        $user = JFactory::getUser();
        $query = "UPDATE #__ablog_posts SET checked_out=" . $user->id . ",checked_out_time=" . $db->quote($date->toSql()) . "WHERE id=" . $id;
        $db->setQuery($query);
        $db->execute();
    }

    public function searchForEditPostId($search_word) {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_posts');
        $app = JFactory::getApplication();
        $search_word = $app->getUserStateFromRequest('com_ablogfiltereditpost', 'filter_search');
        $query = "SELECT id FROM " . $table . " WHERE title LIKE " . $db->quote("%" . $search_word . "%");
        $db->setQuery($query);
        return $db->loadResult();
    }

    public function getCategoriesByParentCategorie($id) {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT * FROM " . $table . " WHERE parent_categorie=" . (int) $id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function getCheckedOutValue($post_id){
        if(isset($post_id)){
            $db = $this->getDbo();
            $table = $db->quoteName('#__ablog_posts');
            $checked_out_name = $db->quoteName('checked_out');
            $id_name = $db->quoteName('id');
            $query = "SELECT " .$checked_out_name . " FROM " . $table . " WHERE " . $id_name . "=" . (int) $post_id;
            $db->setQuery($query);
            return $db->loadObject();
        }
       
    }
    
    public function getCategorieByPostId($post_id){
    	$db = $this->getDbo();
    	$query = $db->getQuery(true);
    	$query
    		->select($db->quoteName('c.title'))
    		->from($db->quoteName("#__ablog_posts","p"))
    		->join('INNER', $db->quoteName('#__ablog_categories', 'c') . ' ON (' . $db->quoteName('p.categorie_id') . ' = ' . $db->quoteName('c.id').')')
    	    ->where($db->quoteName('p.categorie_id').'='.$db->quoteName('c.id'));
    	$db->setQuery($query);
    	return $db->loadObject();    	 
    }

}
