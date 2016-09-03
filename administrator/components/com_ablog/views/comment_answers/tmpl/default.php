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
JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$pagination = $this->getPagination();
$limit_box = $pagination->getListFooter();
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
                    <input type="text" name="filter_search_comment_answers" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="hasTooltip" value="<?php echo JFactory::getApplication()->input->get('filter_search_comment_answers', null, 'string'); ?>" title="<?php //echo JHtml::tooltipText('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
                </div>
                <div class="btn-group pull-left">
                    <button type="submit" id="search_button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT');//echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" id="clean_filter_search_button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR');//JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
                            this.form.submit();"><i class="icon-remove"></i></button>
                </div>
                <div class="btn-group pull-left">
                    <?php echo $pagination->getLimitBox(); ?>
                </div>
            </div>

            <div style="clear:both; margin-bottom: 10px;"></div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="1%" id="th_check">
                            <input type="checkbox" name="checkall-toggle" value="" width="1%" onclick="Joomla.checkAll(this);" />
                        </th>
                        <th width="13%" id="th_email" class="hidden-phone">
                            <?php echo JText::_('COM_ABLOG_EMAIL_ADRESS'); ?>
                        </th>
                        <th width="40%" id="th_content"><?php echo JText::_('COM_ABLOG_CONTENT'); ?></th>
                        <th width="13%" id="th_creator" hidden-phone>
                            <?php echo JText::_('COM_ABLOG_CREATOR'); ?>
                        </th>
                        <th width="13%" id="th_date" class="hidden-phone"><?php echo JText::_('COM_ABLOG_DATE'); ?></th>
                        <th width="1%" id="th_state">
                            <?php echo JText::_('COM_ABLOG_STATE'); ?>
                        </th>
                        <th width="1%" id="th_post_id">
                            <?php echo JText::_('COM_ABLOG_POST_ID'); ?>
                        </th>
                        <th width="1%" id="th_comment_id">
                            <?php echo JText::_('COM_ABLOG_COMMENT_ID'); ?>
                        </th>
                        <th width="1%" id="th_id"><?php echo JText::_('ID'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                   
                    if (isset($this->results)) {
                        foreach ($this->results as $key => $row) {
                            $checked = JHtml::_('grid.id', $key + 1, $row->id);
                            $published = JHtml::_('jgrid.published', $row->published, $key + 1, 'comment_answers.', true);
                            ?>
                            <tr>
                                
                                <td class="checks"><?php echo $checked; ?></td>
                                <td class="email_adresses hidden-phone">
                                    <?php echo $row->email_adress; ?>
                                </td>
                                <td class="contents">
                                    
                                    <?php
                                    if($row->content != ''){ 
                                    	echo $row->content;
                                    }
                                    ?>
                                </td>
                                <td class="creators"><?php echo $row->creator; ?></td>
                                <td class="created_dates hidden-phone">
                                    <?php
                                    echo JHtml::_('date', $row->created_date, $row->created_date);
                                    ?>
                                </td>
                                <td class="published"><?php echo $published; ?></td>
                                <td class="post_ids"><?php echo $row->post_id; ?></td>
                                <td class="comment_ids"><?php echo $row->comment_id; ?></td>
                                <td class="ids"><?php echo $row->id; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
                <input type="hidden" name="option" value="com_ablog" />
                <input type="hidden" name="act" value="comment_answers" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="hidemainmenu" value="0" />
            </table>
            <?php echo JHtml::_('form.token'); ?>
            </form>
            <?php echo $limit_box; ?>