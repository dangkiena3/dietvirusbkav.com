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

class CpanelViewComments extends JViewLegacy {

    protected $canDo;
    protected $sidebar;


    public function display($tpl = null) {
        //all needed data and visual creation here
        $this->canDo = ablogHelper::getActions();


        JHtml::stylesheet('admin.css', 'administrator/components/com_ablog/assets/css/');
        $app = JFactory::getApplication();
        ablogHelper::addSubmenu('comments');
        $model = $this->getModel('comments');
        if (!$model && JDEBUG) {
            throw new Exception('ModelPost for the view not there');
        }
        //Data from ModelCommentsTeaser returns ObjectList
        //Check for Results

        $results = $model->getCommentsTeaser();

        $this->assignRef('results', $results);

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $com_ablog_published = JText::_('COM_ABLOG_PUBLISHED');
        $com_ablog_unpublished = JText::_('COM_ABLOG_UNPUBLISHED');
        $com_ablog_trashed = JText::_('COM_ABLOG_TRASHED');
        $com_ablog_all = JText::_('COM_ABLOG_ALL');
        $com_ablog_created_date = JText::_('COM_ABLOG_CREATED_DATE');
        $com_ablog_creator = JText::_('COM_ABLOG_CREATOR');
        
        JHtmlSidebar::setAction('index.php?option=com_ablog&view=comments');
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_STATE'), 'filter_state', JHtml::_('select.options', array('1' => $com_ablog_published, '0' => $com_ablog_unpublished, '3' => $com_ablog_all), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_state', 'filter_state'), true));
        JHtmlSidebar::addFilter(JText::_('Post_ID'), 'filter_post_id', JHtml::_('select.options', $this->getSelectFieldsValues('post_id'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_post_id', 'filter_post_id'), true));
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_CREATED_DATE'), 'filter_date', JHtml::_('select.options', $this->getSelectFieldsValues('created_date'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_date', 'filter_date'), true));
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_CREATOR'), 'filter_creator', JHtml::_('select.options', $this->getSelectFieldsValues('creator'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_creator', 'filter_creator'), true));
        $this->addToolBar();

        $this->sidebar = JHtmlSidebar::render();

        parent::display();
    }

    //This Function only shows the Select Parameter each is shown only once
    function getSelectFieldsValues($field_data) {
        $model = $this->getModel('comments');
        $results = $model->getDataForView($field_data);

        if ($results) {

            foreach ($results as $list) {

                if ($field_data == 'post_id') {
                    $result_list[] = $list->post_id;
                }
                if ($field_data == 'created_date') {
                    $result_list[] = $list->created_date;
                }
                if ($field_data == 'creator') {
                    $result_list[] = $list->creator;
                }
                if ($field_data == 'comment_id') {
                    $result_list[] = $list->comment_id;
                }
            }
        } else {
            return array();
        }
        return $result_list;
    }

    function addToolBar() {

        JToolBarHelper::title('', 'blog-48');

        if ($this->canDo->get('core.edit')) {
            JToolBarHelper::editList('comments.edit', 'JTOOLBAR_EDIT', true);
        }

        if ($this->canDo->get('core.edit.state')) {
            JToolBarHelper::publish('comments.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('comments.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($this->canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'comments.remove', 'JTOOLBAR_DELETE');
        }

        if ($this->canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_ablog');
            JToolBarHelper::checkin('comments.checkin');
        }
    }

    function getPagination() {
        $model_comments = $this->getModel('comments');
        $total = $model_comments->getTotalComments();
        $limit = aBlogHelper::getLimitComments();
        $limitstarter = aBlogHelper::getLimitStarterComments();
        $pagination = new JPagination($total, $limitstarter, $limit,'comments');
        if($limit > 0 && $limitstarter > 0 && $limitstarter == $limit)$pagination->set('limitstart','0');
        return $pagination;
    }
}