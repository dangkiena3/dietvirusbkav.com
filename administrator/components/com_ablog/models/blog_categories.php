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

class CpanelModelBlog_categories extends JModelLegacy {
	


    function getKategories() {
        $params = JComponentHelper::getParams('com_ablog');
        $limit = aBlogHelper::getLimitABlog();
        $ordering = $this->createOrdering($params);
        $limitstarter = aBlogHelper::getLimitStarterABlog();
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = 'SELECT * FROM ' . $table . " WHERE id > 1 $ordering";
        $db->setQuery($query, $limitstarter, $limit);
        return $db->loadObjectList();
    }

    function getAllKategories() {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = 'SELECT *  FROM ' . $table;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    
    function getCid(){
    	$cid = JFactory::getApplication()->input->get('cid', '', '');
    	
    	if(is_array($cid) && count($cid) == 1 && ctype_digit($cid[0])){
    		return $cid[0];
    	}
    	else if(is_string($cid) && ctype_digit($cid)){
    		return $cid;
    	}
    }
    

    function getKategorie($id) {
        $params = JComponentHelper::getParams('com_ablog');
        $limit = aBlogHelper::getLimitABlog();
        $ordering = $this->createOrdering($params);
        $limitstarter = aBlogHelper::getLimitStarterABlog();
        //id cant be replaced by cid
        //$cid = $this->getCid();
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = 'SELECT * FROM ' . $table . ' WHERE id=' . (int) $id . $ordering;
        $db->setQuery($query, $limitstarter, $limit);
        $result = $db->loadObjectList();
        return $result;
    }

    function delete($cids) {
        $db = $this->getDBO();
        $table_categories = $db->quoteName('#__ablog_categories');
        $table_posts = $db->quoteName('#__ablog_posts');
        $query = "DELETE b FROM " . $table_categories . 
                      " b LEFT JOIN " . $table_posts ." p ON p.categorie_id = b.id 
                      WHERE p.categorie_id IS NULL AND b.id " .
                "IN(" . implode(',', $cids) . ") OR b.parent_id IN(". implode(',',$cids). ")";
        $db->setQuery($query);
        return $db->query();
    }

    function publish() {
        $user = JFactory::getUser();
        $table = $this->getTable('Categorie', 'CategorieTable');
        $cid = JFactory::getApplication()->input->get('cid', '', 'array');
        $table->publish($cid, 1, $user->id);
    }

    function unpublish() {
        $user = JFactory::getUser();
        $table = $this->getTable('Categorie', 'CategorieTable');
        $cid = JFactory::getApplication()->input->get('cid', '', 'array');
        $table->publish($cid, 0, $user->id);
    }

    function storeCategorie() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        //Update entries
        $app = JFactory::getApplication();
       
        $data = $app->input->getArray(
        		array('id' => 'int',
        			  'parent_id' => 'int',
        			  'lft' => 'int',
        			  'rgt' => 'int',
        			  'level' => 'int',
        			  'title' => 'string',
        			  'alias' => 'string',
        			  'access' => 'int',
        			  'checked_out' => 'int',
        			  'checked_out_time' => 'string',
        			  'published' => 'int'        		
        ),$_POST);
        $row = $this->getTable('Categorie', 'CategorieTable');
        $referenceId = $data['parent_id'];

        // Daten an die Tabelle binden
        $row->reset();
        if(!$row->getRootId()){            
            $db = JFactory::getDbo();
            $values = array(1,0,0,1,0,$db->quote('root'),$db->quote('root'),0,$db->quote('root'));
            $query = $db->getQuery(true);            
            $columns = array('id', 'parent_id', 'lft', 'rgt', 'level', 'title', 'alias','access','path');
            $query->insert($db->quoteName('#__ablog_categories'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
            $db->setQuery($query);
            $db->query();
        }
        
        $row->load($data['id']);
        
        $data['id'] > 0 ? $data['id'] : $data['id'] = 0;
  
        if ($row->parent_id != $data['parent_id'] || $data['id'] == 0) {
            $row->setLocation((int) $referenceId, $parent = 'last-child');
        }

        if (!$row->bind($data)) {
            $error = 'Data was not binded';
            if(JDEBUG){
                throw new Exception('Data was not binded');
            }
            return false;
        }

        if (!$row->check()) {
            if(JDEBUG){
                throw new Exception('categorie pass not check');
            }
            return false;
        }
        
        if (!$row->store()) {
            $error = 'Datawas not stored';
            if(JDEBUG){
                throw new Exception('categorie not stored');
            }
            throw new Exception('categorie not stored');
            return false;
        }

        if (!$row->rebuildPath($row->id)) {
            if(JDEBUG){
                throw new Exception('Not rebuiltPath categorie');
            }
        }

        // Rebuild the paths of the category's children:
        if (!$row->rebuild($row->id, $row->lft, $row->level, $row->path)) {
            if(JDEBUG){
                throw new Exception('Not rebuild categorie');
            }
        }
        return true;
    }

    function updateKategorieFields() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Get the table
        $row = $this->getTable('Categorie', 'CategorieTable');
        $data = JFactory::getApplication()->input->getArray(
        array('id' => 'int',
        	  'parent_id' => 'int',
        	  'lft' => 'int',
        	  'rgt' => 'int',
        	  'level' => 'int',
        	  'title' => 'string',
        	  'alias' => 'string',
        	  'access' => 'int',
        	  'checked_out' => 'int',
        	  'checked_out_time' => 'string',
        	  'published' => 'int'
        ),$_POST);
        $date = JFactory::getDate();
        $data['created_date'] = $date->toSql();
        // Daten an die Tabelle binden
        $referenceId = $data['parent_id'];
        $row->reset();
        $id = $data['id'];
        $row->load($id);
        $row->set('id', $id);
        //conditions for update
        if($data['parent_id'] != 0 && $row->parent_id != $data['parent_id'] ){
             $row->setLocation((int) $referenceId, $parent = 'last-child');
        }
        
        if ($data['parent_id'] == 0) {
            $referenceId = 1;
            $row->setLocation((int) $referenceId, $parent = 'last-child');
        }
        
        if (!$row->bind($data)) {
            $error = 'Data was not binded';
            $this->setError($error);
            return false;
        }
        
        if (!$row->check()) {
            if(JDEBUG){
                throw new Exception('categorie pass not check');
            }
            return false;
        }
        
        if (!$row->store()) {
        	if(JDEBUG){
            	$error = 'Data was not stored';
            	$this->setError($error);
        	}
            return false;
        }

        if (!$row->rebuildPath($row->id)) {
            $this->setError($row->getError());
            return false;
        }
        

        if (!$row->rebuild($row->id, $row->lft, $row->level, $row->path)) {
            $this->setError($row->getError());
            return false;
        }
        
        $this->cleanCache();
        
        return true;
    }

    function checkAssignmentToPost($cids) {
        $cid = $this->getCid();
        $db = $this->getDBO();
        $query = "SELECT categorie_id FROM #__ablog_posts  WHERE categorie_id=" . (int) $cid;

        $db->setQuery($query);
        return $db->loadResult();
    }

    function getTotalCategories() {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = 'SELECT * FROM ' . $table;
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return count($results);
    }

    function createOrdering($params) {
        $ordering = $params->get('ablog_backend_ordering_blog_categories');
        $result = ' ORDER BY lft ASC';
        if ($ordering) {
            $result = ' ORDER BY lft DESC';
        }
        return $result;
    }

    function getCheckOut() {
        $db = $this->getDbo();
        $user = JFactory::getUser();
        $cid = $this->getCid();
        $table = $db->quoteName('#__ablog_categories');
        
        if(isset($cid)){
        	$query = 'SELECT checked_out,checked_out_time FROM ' . $table . ' WHERE id=' . (int)$cid;
        	$db->setQuery($query);
        	return $db->loadObject();
        }
    }

    function checkIsSameUserOrZero() {
        $checked_out = $this->getCheckOut();
        $user = JFactory::getUser();
        $user_id = $user->get('id');
        return $checked_out->checked_out == $user_id || $checked_out->checked_out == 0;
    }

    function setCheckOut() {
        $db = $this->getDbo();
        $user = JFactory::getUser();
        $date = JFactory::getDate();
        $cid = $this->getCid();
        $timedate = $date->toSql();
        $user_id = $user->get('id');
        $table = $db->quoteName('#__ablog_categories');
        $query = "UPDATE " . $table . "SET checked_out=" . (int) $user_id . ",checked_out_time=" . $db->quote($timedate) . " WHERE id=" . $cid;
        $db->setQuery($query);
        $db->execute();
    }

    function checkIn() {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $cid = $this->getCid();
        $query = "UPDATE " . $table . "SET checked_out=0,checked_out_time='null' WHERE id=" . (int) $cid;
        $db->setQuery($query);
        return $db->execute();
    }

    function getCategorieObjectById($id) {
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $query = 'SELECT * FROM ' . $table . ' WHERE id=' . (int) $id;
        $db->setQuery($query);
        return $db->loadObject();
    }

    function getCategoriesTree() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__ablog_categories'))->where('id > 1')->order($db->quoteName('lft'));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getParentCategorieByCategorieId($cat) {
        if (isset($cat)) {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('parent_id, lft,level')->from($db->quoteName('#__ablog_categories'))->where($db->quoteName('id') . '=' . (int) $cat);
            $db->setQuery($query);
            return $db->loadObject();
        }
    }

    function getCategorie($id) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('title,id,parent_id,level,lft,path,published')->from($db->quoteName('#__ablog_categories'))->where($db->quoteName('id') . '=' . (int) $id);
        $db->setQuery($query);
        return $db->loadObject();
    }
}