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
jimport('joomla.html.html');

class CpanelViewCategorie extends JViewLegacy {

    protected $canDo;

    function display($tpl = null) {
        $app = JFactory::getApplication();
        $this->canDo = ablogHelper::getActions();
        ablogHelper::addSubmenu('categorie');

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        
        $model = $this->getModel('blog_categories');
        $task = $app->input->getCmd('task');
        $this->addToolBar($task);
        $this->sidebar = JHtmlSidebar::render();
        JHtml::stylesheet('administrator/components/com_ablog/assets/css/admin.css');
        //JHtml::stylesheet('administrator/components/com_ablog/assets/css/toolbar.css');        
        
        parent::display($tpl);
    }

    function addToolBar($task) {
        JToolBarHelper::title('', 'blog-48');
        if ($this->canDo->get('core.edit')) {
        	if($task == 'add'){
        		JToolBarHelper::apply('categorie.save');
        		JToolBarHelper::save('categorie.saveReturn');
        	}else{
        		JToolBarHelper::apply('categorie.saveEdit');
        		JToolBarHelper::save('categorie.saveEditReturn');
        	} 
        }
        JToolBarHelper::cancel('categorie.cancel');
    }
    
    function showParentCategoriesTree(){
       $model = $this->getModel('blog_categories');
       return $model->getCategoriesTree();       
    }
    
    function getParentCategorieByCategorieId($cat){
        $model = $this->getModel('blog_categories');
        return $model->getParentCategorieByCategorieId($cat);
    }
    
    function getCategorie($id){
        $model = $this->getModel('blog_categories');
        return $model->getCategorie($id);
    }
}
