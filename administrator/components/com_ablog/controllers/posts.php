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
defined('_JEXEC') or die('Restricted acess');
//jimport('joomla.application.component.controller');
//require_once JPATH_COMPONENT . '/helpers/ablog.php';





class CpanelControllerPosts extends JControllerLegacy {

    function display($cachable = false, $urlparams = false) {
        $view = $this->getView('posts', 'html');
        $model = $this->getModel('posts', '', $config = array('ignore_request' => true));
        
        $view->setModel($model);
        $results = $model->getPostsTeaser();
        $view->assignRef('results', $results);
        //$app = JFactory::getApplication();
        //$task = $app->input->get('task','','cmd');
        $view->display();
       
    }

    function publish() {
        $model = $this->getModel('posts');
        $model->publish();
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }

    function unpublish() {
        $model = $this->getModel('posts');
        $model->unpublish();
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }

    function trashed() {
        $model = $this->getModel('posts');
        if (!$model->setTrash()) {
            if (JDEBUG) {
                throw new Exception('posts controller setTrashed failed');
            }
        }
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }

    //Delete the Trashed Posts
    function trashedpublished() {
        $model = $this->getModel('posts');
        $cids = JFactory::getApplication()->input->get('cid', '', 'array');
        if (!$model->delete($cids)) {
            if (JDEBUG) {
                throw new Exception('post controller trashed posts not deleted');
            }
        }
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }
    
   

    function edit($key = null, $url = null) {    	
    	$model_posts = $this->getModel('posts');
    	$cid = $model_posts->getCid();
    	$this->handleEditCheckouts($model_posts);
    	
    	if (is_array($cid) && $cid > 1) {
    		$this->setRedirect('index.php?option=com_ablog&act=posts');
    		JFactory::getApplication()->enqueueMessage('Please choose only one row for edit','notice');
    		return false;
    	}
        //toolbar for repeat saving controller in view
        
        $view = $this->getView('post', 'html');
        $model_comments = $this->getModel('comments');
        $view->setModel($model_posts);
        $model_kategories = $this->getModel('blog_categories'); 
        $post = $model_posts->getPost($cid);
        $kategorie_id = $post->categorie_id;

        $kategories = $model_kategories->getKategorie($kategorie_id);

        $view->assignRef('kategories', $kategories);     
        //Get Data from CommentsModel ordered to PostId and set into View       
        $view->assignRef('post', $post);
        $view->display();
    }
    


    private function handleEditCheckouts($model) {
        //set CheckOut Table if not empty
        if ($model->checkOutIsSameUserOrZero() && $this->getActionsEdit()) {
            $model->setCheckOut();
        } else {
            //consider cids
            $url = 'index.php?option=com_ablog&act=posts';
            $this->setRedirect($url, 'Check-out failed with the following error: The user checking out does not match the user who checked out the item. ');
        	$this->redirect();
        }
    }

    public function checkIn() {
    	$app = JFactory::getApplication();
    	$model = $this->getModel('posts');
        $authorised = $this->getAuthorisedUserGroups();
        if($authorised){
        	if(!$model->checkIn()){
        		$app->enqueueMessage('checkIn was not successful, please contact the administrator','notice');	
        	}
        }
        $this->setRedirect('index.php?option=com_ablog&act=posts');
    }
    
    private function getAuthorisedUserGroups() {
    	$model = $this->getModel('posts');
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
