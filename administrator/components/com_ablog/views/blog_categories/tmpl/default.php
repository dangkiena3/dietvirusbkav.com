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
JHtml::_('formbehavior.chosen', 'select');
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
            <div class="btn-group pull-left">
                <?php $pagination = $this->getPagination();
                echo $pagination->getLimitBox();
                ?> 
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th id="checks">
                            <input type="checkbox"
                                   name="checkall-toggle"
                                   value="" onclick="Joomla.checkAll(this)" />
                        </th>
                        <th id="contents"><?php echo JText::_('COM_ABLOG_BLOGCATEGORIES_CATEGORY'); ?></th>
                        <th id="publishes">
<?php echo JText::_('COM_ABLOG_STATE'); ?>
                        </th>
                        <th class="ids"><?php echo JText::_('ID'); ?></th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 0;
                    $strich = '';
                    $tabs = '';
                    if ($this->results) {
                        foreach ($this->results as $i => $row) {
                            //if($row->id == 1)continue;

                            $checked = JHtml::_('grid.id', $i + 1, $row->id);

                            $published = JHtml::_('jgrid.published', $row->published, $i + 1);

                            $link = 'index.php?option=com_ablog&act=blog_categories&task=edit&cid=' . $row->id.'&hidemainmenu=1';
                            ?>                           
                            <tr>
                                <td class="checks"><?php echo $checked; ?></td>
                                <td class="contents"><?php
                                    $counter1 = $row->level;
                                    while ($counter1 > 1) {
                                        echo '&emsp;';
                                        $counter1--;
                                    }
                                    $counter2 = $row->level;
                                    
                                    while ($counter2 > 1) {
                                        echo "|&thinsp;&mdash;";
                                        $counter2--;
                                    }
                                    ?>
                                    <a href="<?php
                                    echo $link;
                                    ?>">
                                           <?php
                                           echo $row->title;
                                           ?>
                                    </a>
                                </td>
                                <td class="published"><?php echo $published; ?></td>
                                <td class="ids"><?php echo $row->id; ?></td>
                            </tr>
       <?php
                       
    }
}
?>
                </tbody>
                <input type="hidden" name="option" value="com_ablog" />               
                <input type="hidden" name="act" value="blog_categories" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
  
            </table>
<?php echo $pagination->getListFooter(); ?>
            </form>
        </div>
    </div>