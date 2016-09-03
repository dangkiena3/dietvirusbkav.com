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
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$editor = JFactory::getEditor();
$current_date = JFactory::getDate(null,JFactory::getConfig()->get('offset'));
$current_date = $current_date->__toString();
$date_formate = '%Y-%m-%d %H:%M:%S';
$app = JFactory::getApplication();
$task = $app->input->getCmd('task');
$level_sign = '';


if ($task === 'add') {
    $post_title = '';
    $kategories = $this->kategories;
    $post_content = '';
    $post_id = '';
} else {
    $post_title = $this->post->title;
    $kategories = $this->kategories;
    $post_content = $this->post->content;
    $post_id = $this->post->id;
}
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {

        if (task == 'post.saveEditReturn')
        {
            Joomla.submitform(task, document.getElementById('post_form'));
        }
        if (task == 'post.apply') {
            Joomla.submitform(task, document.getElementById('post_form'));
        }
        if (task == 'post.apply_edit') {
            Joomla.submitform(task, document.getElementById('post_form'));
        }
        if (task == 'post.cancel') {
            Joomla.submitform(task, document.getElementById('post_form'));
        }
    }
</script>
<form action="index.php" method="post" name="adminForm" id="post_form" class="form-validate form-horizontal">
    <table border="0">
        <tr>
            <td>
                <label for="title"><?php echo JText::_('COM_ABLOG_TITLE'); ?></label>  
            </td>
            <td>
                <input type="text" id="title" name="title" id="title" size="25" value="<?php echo $post_title; ?>" />
            </td>

        </tr>
        <tr>
            <td>
                <label for="categories"><?php echo JText::_('COM_ABLOG_POST_CATEGORIES'); ?></label>  
            </td>
            <td>
                <select id="categories" name="categorie_id">

                    <?php
                    if(isset($this->kategories)){
                       
                        foreach ($this->kategories as $kategorie) {
                            if($kategorie->level > 0){
                             $counter1 = $kategorie->level;
                             $counter2 = $counter1;
                             
                             echo "<option value='$kategorie->id'>";
                                
                                while($counter1 > 1){                                   
                                    $leer = '&emsp;';
                                    echo $leer;
                                    $counter1--;
                                }
                                
                                while ($counter2 > 1) {
                                        $leer = "#";
                                        echo $strich = "&thinsp;&ndash;";
                                        $strich;
                                        $counter2--;
                                }
                               
                            echo $kategorie->title . "</option>";
                            
                        }
                      }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td cols="2">
                <input type="hidden" id="creator_username" name="creator_username" value="<?php echo $this->user_username; ?>" />
            </td>
        </tr>
        <tr>
            <td cols="2">
                <input type="hidden" id="creator" name="creator" id="creator" size="25" value="<?php echo $this->user_name; ?>" />
            </td>
        </tr>
        <tr>
            <td cols="2">
                <input type="hidden" id="created_date" name="created_date" id="created_date" size="25" value="<?php ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="content"><?php echo JText::_('COM_ABLOG_CONTENT'); ?></label>
            </td>
            <td style="height: 100%;">

<?php echo $editor->display('content', $post_content, '100%', 400, 100, 100,FALSE,'ablog_content'); ?>
            </td>
            <td>
               <strong id="start_publishing"><?php echo JText::_('COM_ABLOG_START_PUBLISHING');?></strong><br />
               <?php echo JHtml::_('calendar',$current_date,'publish_start','start_publish',$date_formate); ?><br />
               <strong id="end_publishing"><?php echo JText::_('COM_ABLOG_END_PUBLISHING');?></strong><br />
                <?php echo JHtml::_('calendar',$current_date,'publish_stop','end_publish',$date_formate); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="published"><?php echo JText::_('COM_ABLOG_PUBLISHED'); ?></label>
            </td>
            <td>
                <?php 
                if(isset($this->post->published) && $this->post->published == 1){
                    echo JHtml::_('select.booleanlist', 'published', 'class="inputbox"', '1');
                }else{
                    echo JHtml::_('select.booleanlist', 'published', 'class="inputbox"', '0');
                }
                ?>
            </td>
        </tr>
    </table>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_ablog" />
    <input type="hidden" name="act" value="posts" />   
    <input type="hidden" name="hits" value="0" />
    <input type="hidden" name="ordering" value="0" />
    <input type="hidden" name="checked_out" value="0" />
    <input type="hidden" name="checked_out_time" value="0" />
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
    <input type="hidden" name="cid" value="<?php echo $post_id; ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>