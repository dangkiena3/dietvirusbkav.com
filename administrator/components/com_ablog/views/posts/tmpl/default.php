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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$pagination = $this->getPagination();
?>
<form method="post"  action="index.php" name="adminForm" id="adminForm">
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
                    <label for="search_posts" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                    <input type="text" name="filter_search_posts" id="filter_search_posts" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="hasTooltip" value="<?php echo $app->input->getVar('filter_search_posts', null, 'string'); ?>" title="<?php //echo JHtml::tooltipText('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
                </div>          
                <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip" id="search_button" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT');//echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip" id="clean_search_button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR');//JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search_posts').value = '';
                            this.form.submit();"><i class="icon-remove"></i></button>
                </div>
                
                  <div class="btn-group pull-left">
                      
                <?php echo $pagination->getLimitBox();  ?>  
                      
            </div> 
                
               
            </div>
                        
            
            <div style="clear:both;margin-bottom: 10px;"></div>
            <table class="table table-striped">
                <thead>
                    <tr>      
                        <th id="th_checkbox" width="1%">
                            <input type="checkbox"
                                   name="checkall-toggle"
                                   value="1" onclick="Joomla.checkAll(this);" />
                        </th>
                        <th id="th_title" width="10%" class="hidden-phone"><?php echo JText::_('COM_ABLOG_TITLE'); ?></th>
                        <th id="th_content"><?php echo JText::_('COM_ABLOG_CONTENT'); ?></th>
                        <th id="th_date" width="13%" class="hidden-phone">
<?php echo JText::_('COM_ABLOG_DATE'); ?>
                        </th>
                        <th id="th_creator" width="10%"class="hidden-phone"><?php echo JText::_('COM_ABLOG_CREATOR'); ?></th>
                        <th id="th_state" width="1%">
<?php echo JText::_('COM_ABLOG_STATE'); ?>
                        </th>
                        <th id="th_published" width="1%"><?php echo JText::_('ID'); ?> <?php echo $app->input->getVar('published') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($this->results) {
                        foreach ($this->results as $key => $row) {
                            $checked = JHtml::_('grid.id', $key, $row->id);

                            if ($this->state == -2) {
                                $row->published = $this->state;
                            }

                            if ($this->state == 3 && $row->trashed == 1) {
                                $row->published = -2;
                            }


                            $published = JHtml::_('jgrid.published', $row->published, $key);


                            $link = 'index.php?option='
                                    . $app->input->getVar('option')
                                    . '&act=posts&task=edit&cid=' . $row->id.'&hidemainmenu=1';
                            ?>
                   
                            <tr>

                                <td class="checks"><?php echo $checked; ?></td>
                                <td class="posts_title hidden-phone">
                                    <a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
                                </td>
                                <td class="content"><?php echo $this->buildTeaser($row->content); ?></td>
                                <td class="date hidden-phone">
                                    <?php
                                    echo JHtml::_('date', $row->created_date, $row->created_date);
                                    ?>
                                </td>
                                <td class=" creators hidden-phone"><?php echo $row->creator; ?></td>

                                <td class="published"><?php echo $published; ?></td>                          
                                <td class="ids"><?php echo $row->id; ?></td>
                            </tr>                      
                            <?php
                        }
                    }
                    ?>                       
                </tbody>
                <input type="hidden" name="option" value="com_ablog" />
                <input type="hidden" name="act" value="posts"/>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
            </table>
<?php echo JHtml::_('form.token'); ?>
            </form>
 <?php echo $pagination->getListFooter(); ?>