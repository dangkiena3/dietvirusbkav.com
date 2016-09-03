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
jimport('joomla.html.html');
?>
<form action="#" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <table class="adminform pull-left hidden-phone">
                <tr>
                    <td>              
                    </td>
                    <td>
                        <div id="boxes">
                            <div class="infobox">
                                <h2><?php echo JText::_('COM_ABLOG_INTRODUCTION');?></h2>
                                <h3><?php echo JText::_('COM_ABLOG_THE_FRONTEND');?></h3>
                                <ul>
                                    <li><?php echo JText::_('COM_ABLOG_LI_1');?></li>
                                    <li><?php echo JText::_('COM_ABLOG_LI_2');?></li>
                                    <li><?php echo JText::_('COM_ABLOG_LI_3');?></li>
                                    <li><?php echo JText::_('COM_ABLOG_LI_4');?></li>
                                    <li><?php echo JText::_('COM_ABLOG_LI_5');?></li>
                                </ul>
                            </div>
                            <div class="infobox">
                                <h3><?php echo JText::_('COM_ABLOG_SUPPORT');?></h3>
                                <ul>
                                    <li><?php echo JText::_('COM_ABLOG_CREATED_BY');?> <a href="http://www.webhomeschool.de">www.webhomeschool.de</a></li>
                                </ul>

                                <?php echo JHtml::_('image', 'administrator/components/com_ablog/assets/images/webhomeschool_logo.png', 'webhomeschoollogo'); ?>
                            </div>
                            <div style="margin-top: 5px;">
                                <h3 style="margin-bottom: 5px;"><?php echo JText::_('COM_ABLOG_IF_LIKE');?></h3>
                            </div>
                        </div>
                    </td>            
                </tr>
            </table>
            <?php echo JHtml::_('form.token'); ?>
            </form>
            <?php
            echo JHtml::_('tabs.start', 'info_status');
            echo JHtml::_('tabs.panel', 'Status', 'status_tab_head', array('useCookie' => 1));
            ?>
            <table>
                <tr>
                    <td>AllVideo Plugin</td>
                    <td><?php echo $this->showAllVideoPluginActivated(); ?></td>
                </tr>
            </table>
            <?php
            echo JHtml::_('tabs.end');
            ?>
