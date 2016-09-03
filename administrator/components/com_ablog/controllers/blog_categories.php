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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');


class CpanelControllerBlog_categories extends JControllerLegacy {

    
    // Get and set the Data for the BlogKategories View
    public function display($cachable = false, $urlparams = false) {
        $model = $this->getModel('blog_categories');     
        $view = $this->getView('blog_categories', 'html');
        $view->setModel($model);
        $results = $model->getKategories();
        $all_results = $model->getAllKategories();
        $view->assignRef('all_results',$all_results);
        $view->assignRef('results', $results);
        $view->display();
    }

    public function publish() {
        $model = $this->getModel('blog_categories');
        $model->publish();
        $this->setRedirect('index.php?option=com_ablog&act=blog_categories');
    }

    public function unpublish() {
        $model = $this->getModel('blog_categories');
        $model->unpublish();
        $this->setRedirect('index.php?option=com_ablog&act=blog_categories');
    }    
	
    
     public function edit() {
     	$model = $this->getModel('blog_categories');
     	$cid = $model->getCid();       	
        $this->handleEditCheckOut($model);
        $view = $this->getView('categorie', 'html');
        $view->setModel($model);
        $all_results = $model->getAllKategories();
        $view->assignRef('all_results', $all_results);    
        $results = $model->getKategorie($cid);
        //Take the Kategorie with the checked id     
        $view->assignRef('all_results', $all_results);
        $view->assignRef('results', $results);
        $view->display();
    }

   

    public function remove() {
        $cids = JFactory::getApplication()->input->get('cid','','');
        if(JDEBUG && $cids == ''){
            throw new Exception('controller blog_categories remove no cid');
        }

        $model = $this->getModel('blog_categories');     

        if ($model->checkAssignmentToPost($cids)) {
            $this->setRedirect('index.php?option=com_ablog&act=blog_categories');
            JFactory::getApplication()->enqueueMessage('Kategorie is still Assigned to a Post','notice');
        } else {
            $model->delete($cids);            
            $this->setRedirect('index.php?option=com_ablog&act=blog_categories');
            
            if(JDEBUG && !$model->delete($cids)){
                throw new Exception('blog_categories controller remove not deleted');
            }          
        }
    }


    //Get the CategorieView with SaveButton for storeKategorie()
    public function add() {
        //access if there is not checkout       
        $view = $this->getView('categorie', 'html');
        $model = $this->getModel('blog_categories');
        $all_results = $model->getAllKategories();
        $view->setModel($model);
        $view->display();
    }
    
    private function handleEditCheckOut($model) {
    	//set CheckOut Table if not empty
    	if ($model->checkIsSameUserOrZero() && $this->getActionsEdit()) {
    		$model->setCheckOut();
    	}else{
    		$url = 'index.php?option=com_ablog&act=blog_categories';
    		$this->setRedirect($url, 'Check-out failed with the following error: The user checking out does not match the user who checked out the item. ');
    		$this->redirect();
    	}
    }
    
    public function checkIn(){
        $model = $this->getModel('blog_categories');
        $authorised = $this->getAuthorisedUserGroups();
	
        if($authorised){
	        if(!$model->checkIn()){
	            if(JDEBUG){
	                throw new Exception('blog_categories controller checkIn fail');
	            }
	        }
        }
        $this->setRedirect('index.php?option=com_ablog&act=blog_categories');
    }
    
    private function getAuthorisedUserGroups() {
    	$model = $this->getModel('blog_categories');
    	$checked_out = $model->getCheckOut();
    	$user = JFactory::getUser ();
    	$user_id = $user->get('id');
    	$equal_user = $checked_out->checked_out == $user_id;
  
    	if($equal_user){
    		return $equal_user;
    	}else{
    		foreach($user->getAuthorisedGroups() as $group_user){
    			if($group_user === 7 || $group_user === 8){
    				return true;
    			}
    		}
    	}    	
    }
    
    private function getActionsEdit() {
    	$user = JFactory::getUser ();
    	return $user->authorise ( 'core.edit', 'com_ablog' );
    }
}