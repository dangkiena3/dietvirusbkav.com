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
jimport('joomla.application.component.controller');



class CpanelControllerCategorie extends JControllerLegacy
{
    //Get and set Data for CategorieView
    function display($cachable = false, $urlparams = false) {
	$model = $this->getModel('blog_categories');
        $view = $this->getView('categorie', 'html');        
        $view->setModel($model);
        $view->display();
    }
    //Save FormData if exists
    function save() {
      //JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
      $app = JFactory::getApplication();      
      $model = $this->getModel('blog_categories');
      $data = $app->input->getArray($_POST);     
      //storeCategorie if the input field is not empty     
      if($data['title'] != '') {      
        	$model->storeCategorie();
      	/*if(!$model->storeCategorie()) {
              //throw new Exception('categorie controller store categorie failed');
          }  */       
      }      
      $this->setRedirect('index.php?option=com_ablog&act=blog_categories&task=add');
    }
    
    function saveReturn() {
    	//JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
    	$app = JFactory::getApplication();
    	$model = $this->getModel('blog_categories');
    	$data = $app->input->getArray($_POST);
    	 
    	//storeCategorie if the input field is not empty
    	if($data['title'] != '') {
    		$model->storeCategorie();
    		/*if(!$model->storeCategorie()) {
    		 //throw new Exception('categorie controller store categorie failed');
    		}  */
    	}
    	$this->setRedirect('index.php?option=com_ablog&act=blog_categories');
    }

    
    function saveEdit() {
             //JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
             $model = $this->getModel('blog_categories');
             $app = JFactory::getApplication();
           
            //Get the $data from the form
             $data = $app->input->getArray($_POST); 
 
            //updateKategorie() if the input field is not empty
            if($data['title'] != '') {
            	$model->updateKategorieFields();            	             
            }
            $this->setRedirect('index.php?option=com_ablog&act=blog_categories&task=edit&cid='.(int)$data['id'].'&hidemainmenu=1');    
             
     }
     
     function saveEditReturn() {
     	//JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
     	$model = $this->getModel('blog_categories');
     	$app = JFactory::getApplication();
     	 
     	//Get the $data from the form
     	$data = $app->input->getArray($_POST);
     
     	//updateKategorie() if the input field is not empty
     	if($data['title'] != '') {
     		$model->updateKategorieFields();
     	}
     	$this->setRedirect('index.php?option=com_ablog&act=blog_categories');     	 
     }
     
     function cancel() {
     	$model = $this->getModel('blog_categories');
     	$model->checkIn();
     	$this->setRedirect('index.php?option=com_ablog&act=blog_categories');
     }
}