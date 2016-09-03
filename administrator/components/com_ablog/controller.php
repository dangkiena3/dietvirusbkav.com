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
defined('_JEXEC') or die;
jimport( 'joomla.filesystem.file' );
/**
 * Banners master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class CpanelController extends JControllerLegacy {
     public function display($cachable = false, $urlparams = false) {

        $app = JFactory::getApplication();
        $act = $app->input->getCmd('act', 'cpanel');

        $path = JPath::check(JPATH_COMPONENT . '/controllers/' . $act . '.php');
       
        $file = JFile::exists($path); 
         if(!$file) {
            JError::raiseError('500', JText::_('COM_ABLOG_ERROR_MESSAGE'));
            return false;
         }
        require_once JPATH_COMPONENT .'/controllers/' . $act . '.php';
        $controller = 'CPanelController' . ucfirst($act);
        $controller = new $controller();
        $controller->execute($app->input->getCmd('task'));
        $controller->redirect();
    }
}