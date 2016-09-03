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

class TableComment extends JTable  {
	/** @var int Primary key */
	public $id = '';
        /** @var datetime */
        public $created_date= '';
        /** @var varchar */
        public $email_adress;
        /** @var string */
        public $creator = '';
        /** @var int */
        public $post_creator = 0;
        /** @var text */
        public $content = '';
        /** @var int */
        public $comment_answer_id = '';
        /** @var int */
        public $published = 0;
        /** @int */
        public $post_id = 0;
        /** @int*/
        public $checked_out = 0;
        /** @datetime */
        public $checked_out_time = '';
        
  
	public function __construct( &$db ) {
		parent::__construct( '#__ablog_comments', 'id', $db );
                $date = JFactory::getDate();
		
	}

        public function check() {
            if($creator != '') {
                $this->setError('the creator field is empty');
                return false;
            }else{
                if(JDEBUG){
                    throw new Exception("comment table creator field is empty");
                }
            }
            if($content != '') {
                $this->setError('the content field is empty');
                return false;
            }else{
                if(JDEBUG){
                    throw new Exception("comment table content field is empty");
                }
            }
            return true;
        }
}
?>