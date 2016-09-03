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

class CpanelViewComment_Answers extends JViewLegacy {

    protected $canDo;
    protected $sidebar;


    function display($tpl = null) {
        $this->canDo = ablogHelper::getActions();
      
        $app = JFactory::getApplication();
       
        JHtml::stylesheet('admin.css', 'administrator/components/com_ablog/assets/css/');
        JToolBarHelper::title('', 'blog-48');
        ablogHelper::addSubmenu(JFactory::getApplication()->input->getCmd('act'));

        $model = $this->getModel('comment_answers');
        $results = $model->getCommentAnswersTeaser();
        $this->assignRef('results', $results);

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        $com_ablog_state = JText::_('COM_ABLOG_STATE');
        $com_ablog_published = JText::_('COM_ABLOG_PUBLISHED');
        $com_ablog_unpublished = JText::_('COM_ABLOG_UNPUBLISHED');
        $com_ablog_all = JText::_('COM_ABLOG_ALL');
        $com_ablog_created_date = JText::_('COM_ABLOG_CREATED_DATE');
         
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_STATE'), 'filter_state', JHtml::_('select.options', array('1' => $com_ablog_published, '0' => $com_ablog_unpublished, '3' => $com_ablog_all), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_state', 'filter_state'), true));
        JHtmlSidebar::addFilter(JText::_('Post_ID'), 'filter_post_id', JHtml::_('select.options', $this->getSelectFieldsValues('post_id'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_post_id', 'filter_post_id'), true));
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_CREATED_DATE'), 'filter_date', JHtml::_('select.options', $this->getSelectFieldsValues('created_date'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_date', 'filter_date'), true));
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_CREATOR'), 'filter_creator', JHtml::_('select.options', $this->getSelectFieldsValues('creator'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_creator', 'filter_creator'), true));
        JHtmlSidebar::addFilter(JText::_('COM_ABLOG_COMMENT_ID'), 'filter_comment_id', JHtml::_('select.options', $this->getSelectFieldsValues('comment_id'), 'value', 'text', $app->getUserStateFromRequest('com_ablog' . 'filter_comment_id', 'filter_comment_id'), true));
        
        $this->addToolBar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display();
    }

    //Put the column names into the getDataForView()
    //Get the Data for Filter Fields
    function getSelectFieldsValues($field_data) {
        $model = $this->getModel('comment_answers');
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

                if ($field_data == 'published') {
                    $result_list[] = $list->published;
                }
            }
        } else {
            return array();
        }

        //results are all the data from the selected column in the result_list array
        return $result_list;
    }

    function addToolBar() {

        if ($this->canDo->get('core.edit')) {
            JToolBarHelper::editList('comment_answers.edit', 'JTOOLBAR_EDIT', true);
        }

        if ($this->canDo->get('core.edit.state')) {
            JToolBarHelper::publish('comment_answers.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('comment_answers.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($this->canDo->get('core.delete')) {
            JToolBarHelper::deleteList();
        }

        
        if ($this->canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_ablog');
            JToolBarHelper::checkin('comment_answers.checkIn');
        }
    }

    function getPagination() {
        $model = $this->getModel('comment_answers');
        $app = JFactory::getApplication();        
        $params = JComponentHelper::getParams('com_ablog');
        $total = $model->getTotalCommentAnswers();
        $limit = aBlogHelper::getLimitCommentAnswers();
        $limitstarter = aBlogHelper::getLimitStarterCommentAnswers();        
        $pagination = new JPagination($total, $limitstarter, $limit,'comment_answers');
        if($limit > 0 && $limitstarter > 0 && $limit == $limitstarter)$pagination->set('limitstart','0');
        return $pagination;
    }

}