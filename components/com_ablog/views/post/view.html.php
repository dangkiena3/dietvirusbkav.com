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
require_once JPATH_BASE . '/components/com_ablog/helpers/abloghelper.php';

class ABlogViewPost extends JViewLegacy {

    
    private $dispatcher;

    public function display($tpl = null) {
        JHtml::stylesheet(Juri::root() .  'components/com_ablog/assets/css/post.css');
        JPluginHelper::importPlugin('captcha');
		$this->dispatcher = JDispatcher::getInstance();
        //import repatcha
        //Get Data from PostModel and set into View
        parent::display($tpl);
    }

    protected function getCommentAnswersForView($post_id, $comment_id) {
        $model_comments = $this->getModel('comments');
        return $model_comments->getCommentAnswersForView($post_id, $comment_id);
    }

    protected function cleanInput($text) {
        return strip_tags(htmlspecialchars($text));
    }

    protected function showHits() {
        $params = JComponentHelper::getParams('com_ablog');
        if ($params->get('ablog_show_hits') == 1) {
            return ' | Hits: ' . $this->hits;
        }
    }

    protected function canEdit($checked_out) {
        $user = JFactory::getUser();
        if($user->get('id') == $checked_out && $this->checkAuthorise() || $checked_out == 0 && $this->checkAuthorise()){
            return $user->authorise('core.edit', 'com_ablog');
        }
    }

    protected function getPaginationComments() {     
        $model_comments = $this->getModel('comments');
        $total = $model_comments->getTotalComments();
        $limitstarter = aBlogHelper::getComLimitstarter();
        $params = JComponentHelper::getParams('com_ablog');
        $limit = $params->get('ablog_frontend_comments_limit');
        $pagination = new JPagination($total, $limitstarter, $limit,'com');
        return $pagination;
    }
    
    private function InitCaptcha(){
        $this->dispatcher->trigger('onInit',array('dynamic_recaptcha_1'));
    }
    
    protected function getCaptchaDiv(){
        $this->InitCaptcha();
        return $this->dispatcher->trigger('onDisplay',array('','',''));
    }
    
    
    protected function checkAuthorise(){
         $user = JFactory::getUser();
         return $user->authorise('core.edit');
    }
    
    protected function getCategorieNameByCategorieId($id){
        if(JDEBUG && empty($id) || JDEBUG && !isset($id)){
            throw Exception("Categorie Post is not selected, please contact the administrator");
        }
        $db = JFactory::getDbo();
        $table = $db->quoteName('#__ablog_categories');
        $query = "SELECT title FROM ". $table . " WHERE id=". (int)$id;
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    protected function getPostById(){
        $jinput = JFactory::getApplication()->input;
        $model_posts = $this->getModel('posts');
        $id = $jinput->get('id','','int');
        return $model_posts->getPostById($id);
    }
    
    function getSystemLanguageForSocialButtons($button){
        $language = JFactory::getLanguage();
        if($button == 'google' || $button == 'twitter'){
            $tag = $language->getTag();
            return substr($tag,0,2);
        }else{
            return str_replace('-','_', $language->getTag());
        }
    }
    
    function getLinkForSocialButtons($post,$app,$option){
        if(isset($post->id) >= 1 && !empty($app) && !empty($option)){
            return JUri::base()."index.php/component/ablog/?view=post&id=".$post->id."&title=".$post->title."&Itemid=". $app->getUserStateFromRequest($option . 'Itemid', 'Itemid');
        }else{
            throw new Exception('SocialButtons Data Empty, please contact the Administrator for available support');
        }
    }
}
