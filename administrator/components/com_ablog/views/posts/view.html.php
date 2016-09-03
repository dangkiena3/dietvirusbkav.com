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
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class CpanelViewPosts extends JViewLegacy {

    protected $canDo;
    protected $sidebar;

    public function display($tpl = null) {
        $app = JFactory::getApplication();
        $this->canDo = ablogHelper::getActions();
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('ABLOG_TITLE') . ' :: ' . JText::_('POSTS'));
        //Input the Css-Styles and JS-Scripts
        JHtml::stylesheet('admin.css', 'administrator/components/com_ablog/assets/css/');
        //JHtml::script('jquery.js', '/administrator/components/com_ablog/assets/js/');
        //Input the Bar Icons
        JToolBarHelper::title('', 'blog-48');
        ablogHelper::addSubmenu($app->input->getCmd('act'));
        $this->addToolBar($app);
        $this->sidebar = JHtmlSidebar::render();
        //Get the filterstate from state fields  

        parent::display($tpl);
    }

    protected function buildTeaser($text) {
         $string = strip_tags($text);
         $bbTags = array('!\[b\](.+?)\[/b\]!is', '!\[i\](.+?)\[/i\]!is',
                    '!\[url=(www\..+\.[a-z]{2,6}.*)\](.+)?\[/url\]!i',
                    '!\[url=(http[s]?://.+\.[a-z]{2,6}.*)\](.+)?\[/url\]!i');
        $htmlTags = ' ';
        return preg_replace($bbTags, $htmlTags, $string) . '...';        
    }

    protected function createUntrashItem($key) {
        return '<a class="untrash_item" title="untrash_item" onclick="return listItemTask(\'cb' . $key . '\', \'posts.untrash\')" href="javascript:void(0)"><span>Untrash</span></a>';
    }

    public function addToolBar($app) {

        if ($this->canDo->get('core.create')) {
            JToolBarHelper::addNew('post.add', 'JTOOLBAR_NEW');            
        }

        if ($this->canDo->get('core.edit')) {
            JToolBarHelper::editList('posts.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::checkin('posts.checkin');
        }

        if ($this->canDo->get('core.edit.state')) {
            JToolBarHelper::publish('posts.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('posts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        $state = $app->getUserStateFromRequest('filter_state', 'filter_state', '');
        $this->assignRef('state', $state);
    
        //switch the Deleted and Trash Button with defined state
        if ($state == -2) {
            JToolBarHelper::deleteList('', 'posts.trashedpublished', 'JTOOLBAR_EMPTY_TRASH');
        } else {
            JToolBarHelper::trash('posts.trashed');
        }

        if ($this->canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_ablog');
        }
        
        $com_ablog_published = JText::_('COM_ABLOG_PUBLISHED');
        $com_ablog_unpublished = JText::_('COM_ABLOG_UNPUBLISHED');
        $com_ablog_trashed = JText::_('COM_ABLOG_TRASHED');
        $com_ablog_all = JText::_('COM_ABLOG_ALL');
        
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_FILTER_STATE'), 'filter_state', JHtml::_('select.options', array('1' => $com_ablog_published, '4' => $com_ablog_unpublished, '-2' => $com_ablog_trashed, '3' => $com_ablog_all), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_state', 'filter_state'), true));
    }

    function getPagination() {
        $model_posts = $this->getModel('posts');
        $total = $model_posts->getTotalPosts();
        $limit = aBlogHelper::getLimitPosts();
        $limitstarter = aBlogHelper::getLimitStarterPosts();
        $pagination = new JPagination($total, $limitstarter, $limit,'posts');
        if($limit > 0 && $limitstarter > 0 && $limit == $limitstarter)$pagination->set('limitstart','0');
        return $pagination; 
    }
}