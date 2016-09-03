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
defined('_JEXEC') or die;
JLoader::register('ABlogHelper', dirname(__FILE__) . '/helpers/ablog.php');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('ablog.gettheadminstyle');
// Access check.
// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_ablog')) {
    //create Exeption
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
// Execute the task.
$controller = JControllerLegacy::getInstance('Cpanel');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();