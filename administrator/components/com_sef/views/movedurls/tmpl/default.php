<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.6
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2016 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<script type="text/javascript">
<!--
function handleKeyDown(e)
{
    var code;
    code = e.keyCode;
    
    if (code == 13) {
        // Enter pressed
        document.adminForm.submit();
        return false;
    }
    
    return true;
}

function resetFilters()
{
    document.adminForm.filterOld.value = '';
    document.adminForm.filterNew.value = '';
    document.adminForm.filterDays.value = '0';
    
    document.adminForm.submit();
}
-->
</script>

<?php $this->showInfoText('COM_SEF_INFOTEXT_301REDIRECTS'); ?>

<fieldset>
    <legend><?php echo JText::_('COM_SEF_FILTERS'); ?></legend>
<table>
    <tr>
        <td width="100%" valign="bottom">
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_FILTER_MOVED_FROM_URL') . ':<br />' . $this->lists['filterOld'];
            ?>
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_FILTER_MOVED_TO_URL') . ':<br />' . $this->lists['filterNew'];
            ?>
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_NOT_USED_FOR') . ':<br />' . $this->lists['filterDays'];
            ?>
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo '<br />' . $this->lists['filterReset'];
            ?>
        </td>
    </tr>
</table>
</fieldset>

<table class="adminlist table table-striped">
<thead>
    <tr>
        <th width="5">
            <?php echo JText::_('COM_SEF_NUM'); ?>
        </th>
        <th width="20">
            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
        </th>
        <th class="title">
            <?php echo JHTML::_('grid.sort', 'COM_SEF_MOVED_FROM_URL', 'old', $this->lists['filter_order'] == 'old' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']); ?>
        </th>
        <th class="title">
            <?php echo JHTML::_('grid.sort', 'COM_SEF_MOVED_TO_URL', 'new', $this->lists['filter_order'] == 'new' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']); ?>
        </th>
        <th class="title" width="10%">
            <?php echo JHTML::_('grid.sort', 'COM_SEF_LAST_USED', 'lastHit', $this->lists['filter_order'] == 'lastHit' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']); ?>
        </th>
    </tr>
</thead>
<?php
echo $this->getPaginationFooter(5);
?>
<tbody>
    <?php
    $k = 0;
    //for ($i=0, $n=count( $rows ); $i < $n; $i++) {
    foreach (array_keys($this->items) as $i) {
        $row = &$this->items[$i];
        ?>
        <tr class="<?php echo 'row'. $k; ?>">
            <td align="center">
                <?php echo $this->pagination->getRowOffset($i); ?>
            </td>
            <td>
                <?php echo JHTML::_('grid.id', $i, $row->id ); ?>
            </td>
            <td>
                <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>', 'edit')">
                <?php echo htmlspecialchars($row->old); ?>
                </a>
            </td>
            <td>
                <?php echo htmlspecialchars($row->new); ?>
            </td>
			<td>
				<?php echo (substr($row->lastHit, 0, 10) == '0000-00-00' ? JText::_('COM_SEF_NEVER') : $row->lastHit); ?>
			</td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
</tbody>
</table>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="movedurls" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['filter_order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['filter_order_Dir']; ?>" />
</form>
