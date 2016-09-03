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
 
 
// Don't allow direct linking
defined('_JEXEC') or die('Restricted access');

class CategorieTableCategorie extends JTableNested {
    /*     * @var int Primary key */

    public function __construct(&$_db) {
        parent::__construct('#__ablog_categories', 'id', $_db);
    }
    
    public function setLocation($referenceId, $parent = 'last-child'){
         parent::setLocation($referenceId,$parent = 'last-child');
    }

    public function bind($array, $ignore = '') {
 
        if (isset($array) && is_array($array)) {
            $this->data = $array;
            return parent::bind($array, $ignore);
        } else {
            if(JDEBUG){
                throw new Exception('categorie not bind');
            }
            return false;
        }
    }
    
    /*public function rebuildPath($pk = null) {
        parent::rebuildPath($pk);
    }
    
    public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '') {
        parent::rebuild($parentId, $leftId, $level, $path);
    }*/

    public function store($updateNulls = false) {  
        
        $table = JTable::getInstance('Categorie', 'CategorieTable'); 
        // Attempt to store the data.
        return parent::store($updateNulls);
    }
    

    function check() {
        if (!$this->title == '') {
            return true;
        }else{
            if(JDEBUG){
                throw new Exception('categories table title should not be empty');
            }
        }
        
        if(!$this->published == '' && !is_int($this->published)){
            return true;
        }else{
            if(JDEBUG){
                throw new Exception('categories table published shouldn´t be empty');
            }
        }
        
        if(!$this->parent_id == '' && !is_int($this->parent_id)){
            return true;
        }else{
            if(JDEBUG){
                 throw new Exception('categories table parend_id shouldn´t be empty');
            }
        }
       
        if(!$this->published == '' && !is_int($this->published)) {
            return true;
        }else{
             if(JDEBUG){
                 throw new Exception('categories table published shouldn´t be empty');
            }
        }
        
        if(!$this->id == '' && !is_int($this->id)){
            return true;
        }else{
             if(JDEBUG){
                 throw new Exception('categories table id shouldn´t be empty');
            }
        }
        
        if(!$this->hits == '' && !is_int($this->hits)){
            return true;
        }else{
            
        }
    }
}
?>