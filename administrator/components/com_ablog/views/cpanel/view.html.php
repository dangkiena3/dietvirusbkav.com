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
 
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.html.html');

class  CpanelViewCpanel  extends JViewLegacy {  
    protected $sidebar;
    public function display($tpl = null) {
        
        JHtml::stylesheet('administrator/components/com_ablog/assets/css/admin.css');
        JToolBarHelper::title('','blog-48');
        ablogHelper::addSubmenu('cpanel');
        $this->addToolBar();
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }        
        $this->showAllVideoPluginActivated();

        parent::display($tpl);
    }

    protected function showAllVideoPluginActivated() {
        $allvideo_plugin = JPluginHelper::importPlugin('content', 'jw_allvideos');
        if($allvideo_plugin) {
            return JHtml::_('image', 'administrator/components/com_ablog/assets/images/ok-icon.png', 'accept_icon');
        } else {
           return JHtml::_('image', 'administrator/components/com_ablog/assets/images/no-icon.png', 'all_video_not_installed_image');
        }
    }
    
    protected function addToolBar()
    {
        if (JFactory::getUser()->authorise('core.admin', 'com_ablog')){
            JToolBarHelper::preferences('com_ablog');
        }
        
        $this->sidebar = JHtmlSidebar::render();
    }
}