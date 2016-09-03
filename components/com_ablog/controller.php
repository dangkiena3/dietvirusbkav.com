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
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ABlogController extends JControllerLegacy{
	
	/**
	 * Method to display the view
	 *
	 * @access public
	 *        
	 */
	private $form_errors = '';
	function display($cachable = false, $urlparams = array()) {
		// display all posts
		$app = JFactory::getApplication ();
		$view = $this->getView ( 'post', 'html' );
		$form_errors = $this->form_errors;
		$view->assignRef ( 'form_errors', $form_errors );
		$task = $app->input->getCmd('task');
		
		$jinput = $app->input;
		
		if ($jinput->get ( 'view' ) == 'posts') {
			$this->posts ();
		}
	
		if($task == 'editlayout' && !$this->getActions() || $task == 'savepost' && !$this->getActions()){
			//$app->input->set('view','posts');
			$app->input->set('task','');
			$app->redirect(JRoute::_('index.php?option=com_ablog&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' )));
		}
		
		if($task == 'seeall' && !$this->getActions()){
			$app->input->set('task','');
			$app->redirect(JRoute::_('index.php?option=com_ablog&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' )));
			
		}		
		
		if ($jinput->get ( 'view' ) == 'post') {
			$this->post ();
		}
		
		parent::display();
	}
	function post() {
		$app = JFactory::getApplication ();
		$session = JFactory::getSession ();
		$actions = $this->getActions ();
		$view = $this->getView ( 'post', 'html' );
		$post_id = $app->input->getInt ( 'id' );
		$task = $app->input->getCmd ( 'task' );
		$user = JFactory::getUser ();
		$user_id = $user->get ( 'id' );
		$model_posts = $this->getModel ( 'posts' );
		$checked_out_value = $model_posts->getCheckedOutValue ( $post_id )->checked_out;
		$model_categories = $this->getModel ( 'categories' );
		$model_comments = $this->getModel ( 'comments' );
		
		$row = $model_posts->getPost ( $post_id );
		
		if (empty ( $post_id )) {
			throw new Exception ( "Id is needed for a post" );
		}
		
		if ($task == 'save_comment') {
			$this->save_comment ();
		}
		
		if ($task == 'save_comment_answers') {
			$this->save_comment_answers ();
		}
		// get data when return to post from seeall
		if ($session->get ( 'seeall', 0, 'aBlog' ) == 1 && $actions) {
			$row = $model_posts->getPostEvenNotPublished ( $post_id );
		}
		
		if (empty ( $row ) && ! $actions) {			
			$this->setRedirect ( 'index.php?option=com_ablog&view=posts&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ),'Post is not published','notice');
			$this->redirect();
		}
		
		// get post when authorisized
		if ($app->input->getWord ( 'view' ) == 'post' && $actions && $app->input->getInt ( 'id' ) != '' && $app->input->getInt ( 'Itemid' )) {
			$row = $model_posts->getPostEvenNotPublished ( $post_id );
		}
		
		// get data for editlayout,set edit layout and check_out the user
		if ($task == 'editlayout' && $actions && $user_id == $checked_out_value || $task == 'editlayout' && $actions && $checked_out_value == 0) {
			// get categoriename for view
			$row = $model_posts->getPostEvenNotPublished ( $post_id );
			$view->setLayout ( 'edit' );
			if ($post_id != "" && is_integer ( $post_id )) {
				$model_posts->checkedOut ();
			}
		}
		//redirect to posts view if is not same user
		if($task == 'editlayout' && $actions && $user_id && $checked_out_value > 0 && $user_id != $checked_out_value){
			$this->setRedirect ( 'index.php?option=com_ablog&view=posts&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ),'Another user is working on that Post now' );
		    $this->redirect();
		}
		// go to post view from preview view
		if ($task == 'shownotpublished' && $actions) {
			$row = $model_posts->getPostEvenNotPublished ( $post_id );
		}
		
		// set session notpubliblished on true when was saved as not published
		if ($task == 'savepost' && $app->input->get ( 'published' ) == 0 && $actions) {
			$session->set ( 'notpublished', 1, 'aBlog' );
		}
		// get all not trashed post when session notpublished is true
		if ($session->get ( 'notpublished', '0', 'aBlog' ) == 1 && $actions) {
			$row = $model_posts->getPostEvenNotPublished ( $post_id );
		}
		// redirect after saving post, else get error message, else redirect to normalview posts
		if ($task == 'savepost' && $actions && $this->updatePost ()) {
			$this->setRedirect ( 'index.php?option=com_ablog&view=post&id=' . JRequest::getInt ( 'id' ) . '&title='. $app->input->getString('title').'&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ),'Thank you, your Post is updated now','message' );
			$this->redirect();
		} else if ($task == 'savepost' && $actions && ! $this->updatePost ()) {
			$view->setLayout ( 'preview' );
			$app->enqueueMessage ( "The post update failed, please inform the administrator" );
		} else if ($task == 'savepost' && ! $actions) {
			$this->setRedirect ( 'index.php?option=com_ablog&view=posts&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ) );
			$this->redirect();
		}
		
		if ($app->input->get ( 'cat', '', 'int' ) || $app->input->get ( 'task' ) == 'save_comment_answers') {
			$view->setLayout ( 'commentanswer' );
		}
		
		if ($row) {
			// todo test
			$allvideo_plugin = JPluginHelper::importPlugin ( 'content', 'jw_allvideos' );
			// view important for tasks
			
			$view->setModel ( $model_posts );
			$view->setModel ( $model_comments );
			$params = JComponentHelper::getParams ( 'com_ablog' );
			$this->countHits ();
			$this->displayHits ( $row, $params, $view );
			// Get the AllVideoPlugin
			if ($allvideo_plugin && $app->input->get ( 'view' ) == 'post') {
				$row->text = $row->content;
				// cut the reference
				unset ( $row->content );
				$app = JFactory::getApplication ();
				$params = '';
				$offset = 0;
				$dispatcher = JDispatcher::getInstance ();
				JPluginHelper::importPlugin ( 'content' );
				// $results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$this->params, $offset));
				$dispatcher->trigger ( 'onContentPrepare', array (
						'com_content.article',
						&$row,
						&$this->params,
						$offset 
				) );
			}
			if (isset ( $row->text )) {
				$row->content = $row->text;
				// get the allvideo changed data
				unset ( $row->text );
				$result_post = $row;
			} else {
				$result_post = $row;
			}
			
			// Get Data from CommentsModel ordered to PostId and set into View
			$result_comments = $model_comments->getComments ( $post_id );
			
			$view->assignRef ( 'result_post', $result_post );
			$view->assignRef ( 'result_comments', $result_comments );
		}
	}
	function posts() {
		$app = JFactory::getApplication ();		
		$session = JFactory::getSession ();
		$task = $app->input->get ( 'task' );
		$model_posts = $this->getModel ( 'posts' );
		$model_categories = $this->getModel ( 'categories' );
		$categories = $model_categories->getKategories ();
		$view = $this->getView ( 'posts', 'html' );
		$view->assignRef ( 'form_errors', $form_errors );
		$actions = $this->getActions ();
		$view->assignRef ( 'categories', $categories );
		$post_id = $app->input->getInt ( 'id' );
		
		$categorie_name = $model_posts->getCategorieByPostId ( $post_id );
		
		if ($task == 'editlayout' && $actions || $task == 'savepost' && $actions) {
			$this->showEditLayout ( $view, $task, $categories );
		}
		
		// turn off the notpublished session when enter posts view
		if ($session->get ( 'notpublished', 0, 'aBlog' )) {
			$session->set ( 'notpublished', 0, 'aBlog' );
		}
		
		if ($task == 'seeall' && $actions) {
			$session->set ( 'seeall', 1, 'aBlog' );
		}
		
		// switch between preview and default
		if ($task == 'preview' && $actions) {
			$view->setLayout ( 'preview' );
		} else if ($task != 'preview' && ! $actions) {
			$view->setLayout ( 'default' );
		}
		
		if ($task == 'defaultlayout' && $actions) {
			$session->set ( 'seeall', 0, 'aBlog' );
		}
		// search on seeall view
		
		if ($session->get ( 'seeall', 0, 'aBlog' ) == 1 && $actions && $task != 'editlayout') {
			$post_edit_id = null;
			$search_word = $app->input->get ( 'filter_search' );
			if ($search_word != '') {
				$post_edit_id = $model_posts->searchForEditPostId ( $search_word );
			}
			$view->setLayout ( 'default_edit' );
			
			if (! empty ( $post_edit_id )) {
				$this->setRedirect ( 'index.php?option=com_ablog&view=post&task=editlayout&id=' . $post_edit_id . '&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ) );
				$this->redirect ();
			}
		}
		$view->setModel ( $model_posts );
		$view->setModel ( $model_categories );
		
		// row data
		if ($post_id && isset($categorie_name)) { 
			$categorie_name->title;
			
			if($session->get('seeall',0,'aBlog')){
				$row = $model_posts->getAllIdPostsEdit($post_id);
			}else{
				$row = $model_posts->getAllIdPosts ( $post_id);
			}
			
		} else {
			// for seeall session
			if ($session->get ( 'seeall', 0, 'aBlog' ) == 1 && $actions) {
				$row = $model_posts->getAllPostsEdit ();
				
			} else {
				$row = $model_posts->getAllPosts ();
			}
		}
		// if row exists check jw_allvideo installed and activated
			$allvideo_plugin = JPluginHelper::importPlugin ( 'content', 'jw_allvideos' );
			if ($allvideo_plugin) {
				$view = $this->getView ( 'posts', 'html' );
				// Get the AllVideoPlugin
				if ($allvideo_plugin && $app->input->get ( 'view' ) == 'posts') {
					foreach ( $row as $rowline ) {
						$rowline->text = $rowline->content; // cut the reference
						unset ( $rowline->content );
						
						$app = JFactory::getApplication ();
						$params = '';
						$offset = 0;
						$dispatcher = JDispatcher::getInstance ();
						$results = $dispatcher->trigger ( 'onContentPrepare', array (
								'com_content.article',
								&$rowline,
								&$this->params,
								$offset 
						) );
						$rowline->content = $rowline->text;
						unset ( $rowline->text );
					}
					
					// seperate AllPosts from kategorie posts		
					$posts = $row;
					
				}
			}
			$posts = $row;
			$view->assignRef ( 'posts', $posts );

	}
	private function getActions() {
		$user = JFactory::getUser ();
		return $user->authorise ( 'core.edit', 'com_ablog' );
	}
	protected function displayHits($row, $params, $view) {
		$show_hits = $params->get ( 'ablog_show_hits' );
		$hits = $row->hits;
		$view->assignRef ( 'hits', $hits );
	}
	function save_comment() {
		JSession::checkToken () or jexit ( JText::_ ( 'INVALID TOKEN' ) );
		$dispatcher = JDispatcher::getInstance ();
		$plugin = JPluginHelper::importPlugin ( 'captcha' );
		$model_comments = $this->getModel ( 'comments' );
		$app = JFactory::getApplication ();
		$post_id = $app->input->get ( 'post_id' );
		$data = $app->input->getArray ( array (
				'title' => 'string',
				'ablog_front_content' => 'raw',
				'published' => 'int',
				'hits' => 'int',
				'creator' => 'alnum',
				'creator_username',
				'creator_id' => 'int',
				'categorie_id' => 'int',
				'trashed' => 'int',
				'publish_start' => 'string',
				'publish_stop' => 'string',
				'created_date' => 'string',
				'email_adress' => 'string',
				'post_id' => 'int' 
		), $_POST );
		
		$data ['ablog_front_content'] = aBlogHelper::purifyHtmlCode ( $data ['ablog_front_content'] );
		$this->form_errors = '';
		if ($plugin) {
			$res = $_POST ['g-recaptcha-response'];
			$res_checked = $dispatcher->trigger ( 'onCheckAnswer', array (
					$res 
			) );
			
			if (! $res_checked [0]) {
				$this->form_errors .= 'Captcha not passed';
			}
		}
		
		$email_field = $data ['email_adress'];
		
		$is_mail = JMailHelper::isEmailAddress ( $email_field );
		
		if ($is_mail) {
			$email_cleaned = JMailHelper::cleanAddress ( $email_field );
		} else {
			$this->form_errors .= 'Please enter an emailadress into the emailfield<br />';
			$email_cleaned = null;
		}
		
		if ($data ['creator'] == '')
			$this->form_errors .= 'Please enter a creator name into the creator field<br />';
		if ($data ['ablog_front_content'] == '')
			$this->form_errors .= 'Please enter a content into the content field';
		
		if (count ( $data ['ablog_front_content'] ) >= 1000) {
			$this->form_errors .= 'The max word number is reached';
		}
		
		$view = $this->getView ( "post", "html" );
		$form_errors = $this->getFormErrors ();
		$view->assignRef ( 'form_errors', $form_errors );
		
		if (empty ( $this->form_errors )) {
			$data ['email_adress'] = $email_cleaned;
			// show Comments without Administrator
			$params = JComponentHelper::getParams ( 'com_ablog' );
			$params_publishing = $params->get ( 'publishing_comments' );
			// determine if the comments are immediately published or not
			if ($params_publishing == 1) {
				if (! $model_comments->storeAndPost ( $data )) {
					$app->enqueueMessage ( 'An error accured, please inform the site administrator', 'error' );
				} else {
					// $model_comments->informBlogger($post_id);
					$this->setRedirect(JRoute::_('index.php?option=com_ablog&view=post&id='.$app->input->get('id','','int').'&title='. $app->input->getString('title').'&Itemid='.$app->getUserStateFromRequest('com_ablogItemid', 'Itemid')));
					$this->redirect();
				}
			} else {
				if (!$model_comments->store ( $data )) {
					$app->enqueueMessage ( 'An error accured, please inform the site administrator', 'error' );
				} else {
					$this->setRedirect ( JUri::base () . 'index.php?option=com_ablog&view=post&id=' . $app->input->get ( 'id', '', 'int' ) . '&title='. $app->input->getString('title') .'&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ),'Thank you, your comment have been stored','message' );
					$this->redirect();	
				}
			}
		}
	}
	function save_comment_answers() {
		JSession::checkToken () or jexit ( JText::_ ( 'INVALID TOKEN' ) );
		$view = $this->getView ( 'post', 'html' );
		$model_comments = $this->getModel ( 'comments' );
		$app = JFactory::getApplication ();
		
		$post_id = $app->input->getInt ( 'id' );
		
		if (! $post_id && JDEBUG) {
			throw new Exception ( 'ABlogController->save_comment_answers() / $post_id not exists' );
		}
		$params = JComponentHelper::getParams ( 'com_ablog' );
		
		$params_publish = $params->get ( 'publishing_comments' );
		$postArray = $app->input->getArray ( array (
				'creator' => 'string',
				'email_adress' => 'string',
				'ablog_front_content' => 'raw',
				'created_date' => 'string',
				'post_id' => 'int',
				'id' => 'int',
				'comment_id' => 'int' 
		), $_POST );
		// From the PostField comment_id
		$postArray ['ablog_front_content'] = aBlogHelper::purifyHtmlCode ( $postArray ['ablog_front_content'] );
		$this->form_errors = '';
		$plugin = JPluginHelper::importPlugin ( 'captcha' );
		if ($plugin) {
			$dispatcher = JDispatcher::getInstance ();
			$res = $_POST ['g-recaptcha-response'];
			$res_checked = $dispatcher->trigger ( 'onCheckAnswer', array (
					$res 
			) );
			if (! $res_checked [0]) {
				$this->form_errors .= "Captcha not passed";
			}
		}
		
		$comment_id = $postArray ['comment_id'];
		
		if (! $comment_id && JDEBUG) {
			throw new Exception ( 'ABlogController->save_comment_answers() / $comment_id not exists' );
		}
		
		$comments = $model_comments->getCommentByPostId ( $post_id, $comment_id );
		$data = $postArray;
		
		$model_posts = $this->getModel ( 'posts' );
		
		$is_mail = JMailHelper::isEmailAddress ( $data ['email_adress'] );
		
		if ($is_mail) {
			$email_cleaned = JMailHelper::cleanAddress ( $data ['email_adress'] );
		} else {
			$this->form_errors .= 'Please enter an emailadress into the emailfield from commentanswers<br />';
			$email_cleaned = null;
		}
		
		if ($data ['creator'] == '') {
			$this->form_errors .= 'Please enter the creator name into the creator field<br />';
		}
		if ($data ['ablog_front_content'] == '') {
			$this->form_errors .= 'Please enter the content name into the content field';
		}
		
		if (count ( $data ['ablog_front_content'] ) >= 1000) {
			$this->form_errors = 'Please enter less than 1000 signs';
		}
		
		$form_errors = $this->form_errors;
		
		$view->assignRef ( 'form_errors', $form_errors );
		
		if (empty ( $this->form_errors )) {
			
			if ($params_publish != 0) {
				if (! $model_comments->storePublishCommentAnswer ()) {
					$app->enqueueMessage ( 'An error accured, please inform the site administrator', 'warning' );
				} else {					
					$this->setRedirect ( JRoute::_('index.php?option=com_ablog&view=post&id=' . $app->getUserStateFromRequest ( 'com_ablogid', 'id' ) . '&title='. $app->input->getString('title') .'&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' ),'Thank you, your comment answer were stored','message' ));
					$this->redirect();
				}
			} else {
				if (! $model_comments->storeCommentAnswer ()) {
					$app->enqueueMessage ( 'An error accured, please inform the site administrator', 'warning' );
				} else {
					$app->enqueueMessage ( 'Thank you, your comment answer were stored','message' );
					$this->setRedirect ( JRoute::_('index.php?option=com_ablog&view=post&id=' . $app->getUserStateFromRequest ( 'com_ablogid', 'id' ) .'&title='. $app->input->getString('title') .'&Itemid=' . $app->getUserStateFromRequest ( 'com_ablogItemid', 'Itemid' )));
					$this->redirect();					
				}
			}
		}
		$view->setModel ( $model_comments );
		$view->setModel ( $model_posts );
	}
	function countHits() {
		$model = $this->getModel ( 'posts' );
		$model->updatePostHits ();
	}
	private function getCommentAnswersById($post_id, $comment_id) {
		$model_comments = $this->getModel ( 'comments' );
		return $model_comments->getCommentAnswersById ( $post_id, $comment_id );
	}
	private function getKategories() {
		$model_categories = $this->getModel ( 'categories' );
		return $model_categories->getKategories ();
	}
	private function getPostsById($id) {
		$model_posts = $this->getModel ( 'posts' );
		return $model_posts->getPost ( $id );
	}
	private function getFormErrors() {
		return $this->form_errors;
	}
	private function savePost() {
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		$model_posts = $this->getModel ( 'posts' );
		$app = JFactory::getApplication ();
		$jinput = $app->input;
		$view = $this->getView ( 'posts', 'html' );
		$data = $app->input->getArray ( array (
				'title_edit' => 'string',
				'edit_kategorie' => 'string',
				'published' => 'int',
				'ablog_front_content' => 'raw',
				'publish_start' => 'string',
				'publish_stop' => 'string' 
		), $_POST );
		
		$data ['ablog_front_content'] = aBlogHelper::filterText ( $data ['ablog_front_content'] );
		
		if ($data ['title_edit'] == '')
			$this->form_errors .= 'Please enter the title<br />';
		
		if ($data ['edit_kategorie'] == '' && JDEBUG) {
			throw new Exception ( 'ablog frontend controller method savePost no $data["edit_kategorie"]' );
		}
		
		if ($data ['ablog_front_content'] == '') {
			$this->form_errors .= 'Please enter the content field';
		}
		
		$form_errors = $this->form_errors;
		
		$view->assignRef ( 'form_errors', $form_errors );
		
		if (empty ( $this->form_errors )) {
			// if no error
			if (! $model_posts->storePostEdit ( $data )) {
				$id = $model_posts->getLastInsertedPost ()->id;
				$this->setRedirect ( 'index.php?option=com_ablog&view=posts&task=preview&id=' . $id );
				$this->redirect ();
				// send last inserted id to preview layout
				
				$app->enqueueMessage ( 'Thank you, your post where stored', 'message' );
			} else {
				$app->enqueueMessage ( 'Post not stored', 'error' );
			}
		}
	}
	private function updatePost() {
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		$model_posts = $this->getModel ( 'posts' );
		$app = JFactory::getApplication ();
		$jinput = $app->input;
		$view = $this->getView ( 'posts', 'html' );
		$data = $jinput->getArray ( array (
				'title_edit' => 'string',
				'published' => 'int',
				'ablog_front_content' => 'raw',
				'publish_start' => 'cmd',
				'publish_stop' => 'cmd' 
		), $_POST );
		$data ['id'] = $jinput->getInt ( 'id' );
		
		$data ['ablog_front_content'] = aBlogHelper::filterText ( $data ['ablog_front_content'] );
		
		if ($data ['title_edit'] == '')
			$this->form_errors .= 'Please enter the title<br />';
		
		if ($data ['ablog_front_content'] == '') {
			$this->form_errors .= 'Please enter the content field';
		}
		
		if (JDEBUG && $data ['published'] === '') {
			throw new Exception ( 'frontend controller updatePost published empty' );
		}
		
		if (JDEBUG && $data ['publish_start'] == '') {
			throw new Exception ( 'frontend controller updatePost publish_start empty' );
		}
		
		if (JDEBUG && $data ['publish_stop'] == '') {
			throw new Exception ( 'frontend controller updatePost publish_end empty' );
		}
		
		$form_errors = $this->form_errors;
		$view->assignRef ( 'form_errors', $form_errors );
		
		$model_posts->updatePostEdit ( $data );
		
		if (empty ( $this->form_errors )) {
			return $model_posts->updatePostEdit ( $data );
		}
	}
	private function showEditLayout($view, $task, $categories) {
		$view->setLayout ( 'edit' );
		
		if ($task == 'savepost' && ! empty ( $categories )) {
			$this->savePost ();
		} else if (empty ( $categories )) {
			throw new Exception ( 'Please create a new Categorie before the Post' );
		}
	}
}
