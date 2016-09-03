<?php
/**
 * Webhomeschool Component
 * @package ABlog
 * @subpackage Controllers
 *
 * @copyright (C) 2013 Webhomeschool. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.webhomeschool.de
 **/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Load the base JModel class
/**
* Revue Model
*/
class ABlogModelCategories extends JModelLegacy
{
    
    
    public function getKategories() {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT * FROM" . $table . ' WHERE id > 1 ORDER BY id ASC';
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function getCategoriesForEditPosts(){
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT * FROM" . $table;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function getCategorieById($id){
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT title FROM ".$table. " WHERE id=" . (int)$id;
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    public function getChildCategoriesByCategorieId($id){
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT title,id FROM ".$table. " WHERE parent_id=". (int)$id;
        $db->setQuery($query);
        $row[$id] = $db->loadAssocList();
 
        if(!empty($row[$id])){
            return $row;
        }
    }
    
    public function getCategorieByIdForMenu($id){
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT * FROM ".$table. " WHERE id=" . (int)$id;
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    public function getCategoriesOrderByLeft() {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT id,title,level,rgt,lft, CASE WHEN rgt-lft > 1 THEN '1' ELSE '0' END AS button FROM " . $table . 'WHERE published=1 ORDER BY lft ASC';
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}