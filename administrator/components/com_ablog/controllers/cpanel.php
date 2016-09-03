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
 
defined('_JEXEC') or die ('Restricted access');
require_once JPATH_COMPONENT.'/helpers/ablog.php';

class CpanelControllerCpanel extends JControllerLegacy
{
	//Get only the Cpanel View
        function display($cachable = false, $urlparams = false) {
            $view = $this->getView('cpanel', 'html');
            parent::display();
            return $this;
	}
}
