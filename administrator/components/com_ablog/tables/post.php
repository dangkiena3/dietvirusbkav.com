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
// Don't allow direct linking
defined('_JEXEC') or die('Restricted access');

class TablePost extends JTable {

    /** @var int Primary key */
    public $id = '';

    /** @var string */
    public $title = '';

    /** @var text */
    public $content = '';

    /** @var int */
    public $checked_out = 1;

    /** @var datetime */
    public $checked_out_time = '';
    /** @var int **/
    public $trashed = 0;

    /** @var int */
    public $published = 0;

    /** @var datetime */
    public $created_date = '';

    /** @var int */
    public $hits = 0;

    /** @var varchar */
    public $creator = '';

    /** @var int */
    public $categorie_id = 0;
    
    public $creator_id = 0;

    /** @var string */
    public $creator_username = '';
    
    public $publish_start = '00-00-00 00:00:00';
    
    public $publish_stop = '00-00-00 00:00:00';

    public function __construct(&$_db) {
        parent::__construct('#__ablog_posts', 'id', $_db);
        $date = JFactory::getDate();
        $this->created_date = $date->toSql();
    }

    public function check() {
        if ($this->title != '') {
            return true;
        }else{
            if(JDEBUG){
                throw new Exception("post table title shouldn´t be empty");
            }
        }
        if ($this->content != '') {
            return true;
        }else{
            if(JDEBUG){
                throw new Exception("ablog_post_content shouldn´t be empty");
            }
        }

        if (is_integer($this->categorie_id)) {
            return true;
        }else{
            if(JDEBUG){
                throw new Exception("categorie_id shouldn´t be empty");
            }
        }

        if (is_integer($this->id)) {
            return true;
        }else{
            if(JDEBUG){
                throw new Exception("table id shouldn´t be number");
            }
        }
        if (is_integer($this->published)) {
            return true;
        }else{
            if(JDEBUG){
                throw new Exception("published shouldn´t be empty");
            }
        }
        
        if($this->publish_start < $this->publish_end){
            return true;
        }

        return false;
    }
}
?>