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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class CpanelViewBlog_Categories extends JViewLegacy {

    protected $canDo;
    protected $sidebar;

    function display($tpl = null) {
        // Include the component HTML helpers.
        $this->canDo = ablogHelper::getActions();
        $app = JFactory::getApplication();
        $option = 'com_ablog';
        ablogHelper::addSubmenu(JFactory::getApplication()->input->getCmd('act'));

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    protected function addToolBar() {
        if ($this->canDo->get('core.create')) {
            JToolBarHelper::addNew('blog_categories.add');
        }

        if ($this->canDo->get('core.edit')) {
            JToolBarHelper::editList('blog_categories.edit');
            JToolBarHelper::checkin('blog_categories.checkin');   
        }


        if ($this->canDo->get('core.edit.state')) {
            JToolBarHelper::publish('blog_categories.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('blog_categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($this->canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'blog_categories.remove', 'JTOOLBAR_DELETE');
        }
        if ($this->canDo->get('core.admin') || $this->canDo->get('core.options')) {
            JToolBarHelper::preferences('com_ablog');            
        }        

        $this->sidebar = JHtmlSidebar::render();
    }

    protected function setDocument() {        
        JToolBarHelper::title('','blog-48');
    }

    function getPagination() {
        $model_categories = $this->getModel('blog_categories');
        $app = JFactory::getApplication();
        $session = JFactory::getSession();
        $params = JComponentHelper::getParams('com_ablog');
        $total = $model_categories->getTotalCategories() - 1;    
        $limitstarter = aBlogHelper::getLimitStarterABlog();
        $limit = aBlogHelper::getLimitABlog();
        $pagination = new JPagination($total, $limitstarter, $limit,'ablog');
        if($limit == $limitstarter && $limit > 0 && $limitstarter > 0)$pagination->set('limitstart','0');
        return $pagination;
    }
    
    function getCategorieObjectById($parent_id){
        $model_categorie = $this->getModel('blog_categories');
        return $model_categorie->getCategorieObjectById($parent_id);
    }
}