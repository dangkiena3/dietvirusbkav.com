<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.6
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2016 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SEFControllerStatistics extends SEFController {
    function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'statistics');
        
        parent::display();
    }
    
}
?>