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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class SefViewMetatags extends SefView
{
	function display($tpl = null)
	{
	    $icon = 'manage-tags.png';
		JToolBarHelper::title(JText::_('COM_SEF_JOOMSEF_META_TAGS_MANAGER'), $icon);
		
        $this->assign($this->getModel());
        
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::back('COM_SEF_BACK', 'index.php?option=com_sef');
        
		// Get data from the model
        $this->assign('items', $this->get('Data'));
        $this->assign('total', $this->get('Total'));
        $this->assign('lists', $this->get('Lists'));
        $this->assign('pagination', $this->get('Pagination'));
        
        JHTML::_('behavior.tooltip');
        
		parent::display($tpl);
	}

}
