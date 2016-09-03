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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class CpanelViewComment extends JViewLegacy {
    
    protected $canDo;
    protected $sidebar;

    function display($tpl = null) {
        JHtml::stylesheet('admin.css', 'administrator/components/com_ablog/assets/css/');
        
        $this->canDo = ablogHelper::getActions();
        
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        
        ablogHelper::addSubmenu(JFactory::getApplication()->input->getCmd('act'));
        JHtmlSidebar::addFilter(JText::_('state'),'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'),'value', 'text', 1, true));
        $this->addToolBar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display();
    }

    function addToolBar() {
        
         JToolBarHelper::title('', 'blog-48');
        
        
        if ($this->canDo->get('core.create')) {
            
            JToolBarHelper::save('comment.save');
        }
        
         JToolBarHelper::cancel('comment.cancel');
    }

}
