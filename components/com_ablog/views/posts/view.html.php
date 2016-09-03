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
jimport('joomla.application.component.view');
jimport('joomla.application.menu');
jimport('joomla.html.pagination');
jimport('joomla.html.parameter');

class ABlogViewPosts extends JViewLegacy {

    private $main_categorie;
    private $sub_categorie;

    function display($tpl = null) {
        JHtml::stylesheet(JUri::root() . 'components/com_ablog/assets/css/posts.css');

        $model_posts = $this->getModel('posts');
        $id = JFactory::getApplication()->input->getInt('id');

        parent::display($tpl);
    }

    function getPagination() {
    	$model = $this->getModel('posts');
    	return $model->createPagination();
    }

    function getPaginationEdit() {
        $model = $this->getModel('posts');
        return $model->createPaginationEdit();
    }

    function getPostsData($model_posts) {
        $results = $model_posts->getAllPosts();
        return $results;
    }    
    
    function mainCategories(){
        $app = JFactory::getApplication();
        $option = 'com_ablog';
        $item_id = $app->getUserStateFromRequest($option . 'Itemid', 'Itemid');
        $model = $this->getModel('categories');
        $menu_link_data = $model->getCategoriesOrderByLeft();

        foreach($menu_link_data as $categorie){
        	$this->categorien = $menu_link_data;
            echo ($categorie->button == 1) ? '<li><a href="'. JRoute::_('index.php?option=com_ablog&view=posts&id='. $categorie->id.'&Itemid='.$item_id).'">'.$categorie->title.'</a></li><li><a>&rsaquo;</a></li>':'<li><a href="'. JRoute::_('index.php?option=com_ablog&view=posts&id='. $categorie->id.'&Itemid='.$item_id).'">'.$categorie->title.'</a></li>';
            
        }
    }
    
    function checkAuthorise() {
        $user = JFactory::getUser();
        return $user->authorise('core.edit');
    }

    function getPostById() {
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id');
        if (JDEBUG && empty($id)) {
            throw new Exception('posts view getPostById no id');
        }
        $model_posts = $this->getModel('posts');
        return $model_posts->getPostById($id);
    }

    function getPaginationPosts(){
    	$model = $this->getModel('posts');
    	return $model->createPaginationPosts();
    }

    function getChildCategories($id) {
        $model = $this->getModel('categories');
        return $model->getChildCategoriesByCategorieId($id);
    }
}
