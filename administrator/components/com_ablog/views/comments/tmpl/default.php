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
$app = JFactory::getApplication();
JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$pagination = $this->getPagination();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                    <input type="text" name="filter_search_comments" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="hasTooltip" value="<?php echo $app->input->getVar('filter_search', null, 'string'); ?>" title="<?php //echo JHtml::tooltipText('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
                </div>
               
                <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip" id="search_button" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT');//echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip" id="clean_search_button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR');//JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
                            this.form.submit();"><i class="icon-remove"></i></button>
                </div>
                
                <div class="btn-group pull-left">
<?php echo $pagination->getLimitBox(); ?>
                </div>
               
            </div>
            <div style="clear:both;margin-bottom: 10px;"></div>
            <table class="table table-striped">
                <thead>
                    <tr>
                                
                        <th width="1%" id="th_check">
                            <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />                         
                        </th>
                        <th width="" id="th_email" class="hidden-phone">
                            <?php echo JText::_('COM_ABLOG_EMAIL_ADRESS'); ?>
                        </th>
                        <th width="40%" id="th_content"><?php echo JText::_('COM_ABLOG_CONTENT'); ?></th>
                        <th width="13%" id="th_creator" class="content hidden-phone">
<?php echo JText::_('COM_ABLOG_CREATOR'); ?>
                        </th>
                        <th width="13%" class="hidden-phone" id="th_date" ><?php echo JText::_('COM_ABLOG_DATE'); ?></th>
                        <th width="1%" id="th_state">
<?php echo JText::_('COM_ABLOG_STATE'); ?>
                        </th>
                        <th width="1%" id="th_post_id">
<?php echo JText::_('COM_ABLOG_POST_ID'); ?>
                        </th>
                        <th width="1%" id="th_id"><?php echo JText::_('ID'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $filter_state = $app->getUserStateFromRequest('com_ablog' . 'filter_state', 'filter_state');
           
               
                    if ($this->results) {
                        foreach ($this->results as $key => $row) {
                            $checked = JHtml::_('grid.id', $key + 1, $row->id);
                            $published = JHtml::_('jgrid.published', $row->published, $key + 1, 'comments.', true);
                            $link = JRoute::_('index.php?option='
                                            . $app->input->getVar('option')
                                            . '&task=edit&cid[]=' . $key
                                            . '&hidemainmenu=1');
                            ?>
                            <tr>
                                
                                <td class="checks"><?php echo $checked; ?></td>
                                <td class="comments_email hidden-phone">
        <?php echo $row->email_adress; ?>
                                </td>
                                <td class="content">
        <?php echo strip_tags($row->content); ?>
                                </td>
                                <td class="creator hidden-phone"><?php echo $row->creator; ?></td>
                                <td class="comments_date hidden-phone">
                                    <?php echo JHtml::_('date', $row->created_date, $row->created_date); ?>
                                </td>
                                <td class="published"><?php echo $published; ?></td>
                                <td class="post_ids"><?php echo $row->post_id; ?></td>
                                <td class="ids"><?php echo $row->id; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
                <input type="hidden" name="option" value="com_ablog" />
                <input type="hidden" name="act" value="comments" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
            </table>
            </form>
            <?php echo $pagination->getListFooter(); ?>