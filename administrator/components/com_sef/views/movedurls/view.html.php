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

class SefViewMovedurls extends SefView
{
	function display($tpl = null)
	{
		JToolBarHelper::title( JText::_('COM_SEF_301_REDIRECTS_MANAGER'), '301-redirects.png' );
		
		$bar = JToolBar::getInstance();
		
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList('COM_SEF_ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_URLS');
		JToolBarHelper::spacer();
		$bar->appendButton( 'Confirm', 'COM_SEF_CONFIRM_DEL_FILTER', 'delete_f2', 'COM_SEF_DELETE_ALL_FILTERED', 'deletefiltered', false, false );
		JToolBarHelper::spacer();
		JToolBarHelper::back('COM_SEF_BACK', 'index.php?option=com_sef');
		
		// Get data from the model
        $this->assign('items', $this->get('Data'));
        $this->assign($this->getModel());
        $this->assign('total', $this->get('Total'));
        $this->assign('lists', $this->get('Lists'));
        $this->assign('pagination', $this->get('Pagination'));
        
		parent::display($tpl);
	}
	
}
