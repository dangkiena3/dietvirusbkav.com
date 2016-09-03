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
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableComment_Answer extends JTable  {
	/** @var int Primary key*/
		public $id = 0;
        /** @var datetime */
        public $created_date = '';
        /** @var string */
        public $creator = '';
        /** @var text*/
        public $content = '';        
        /** @var int */       
        public $published = 0;
                
	public function __construct( &$db ) {
		parent::__construct( '#__ablog_comment_answers', 'id', $db );
                $date = JFactory::getDate();
		$this->created_date = $date->toSql();
	}
}
?>
