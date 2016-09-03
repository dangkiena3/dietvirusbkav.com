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
// Create the controller
$controller = JControllerLegacy::getInstance('aBlog');
//Perform the requested task
$controller->execute('display');
//Redirect if set by the controller
?>