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
JHtml::_('behavior.keepalive');
echo JHtml::stylesheet('admin.css', 'administrator/components/com_ablog/assets/css/');
$app = JFactory::getApplication();
$cid = $app->input->get('cid','','array');
if(!empty($cid) > 0){
	$cat_id = $cid[0];
}


if (
        !isset($cat_id) && $app->input->getCmd('task') == 'edit' ||
        !isset($cat_id) && $app->input->getCmd('task') == 'saveEdit' ||
        !isset($cat_id) && $app->input->getCmd('task') == 'publish' ||
        !isset($cat_id) && $app->input->getCmd('task') == 'unpublish' ||
        !isset($cat_id) && $app->input->getCmd('task') == 'delete' ||
        !isset($cat_id) && $app->input->getCmd('task') == 'checkin'
    ) {
    //throw new Exception("Please describe the display content and inform the administrator");
}
$counter = 0;
$strich = '';
$leer = '';
$tabs = '';
$parent_id = 0;

//For deleting
 if (isset($cat_id) && $app->input->getCmd('task') == 'delete') {
  $parent_id_from_cat_id = $this->getParentCategorieByCategorieId($cat_id);
  $app->setUserState('form_level', $parent_id_from_cat_id->level);
  $app->setUserState('form_parent', $parent_id_from_cat_id->parent_id);
  $app->setUserState('form_lft', $parent_id_from_cat_id->lft);
  }
$task = $app->input->getCmd('task');
if ($task == 'edit') {
    $categorie = $this->getCategorie($cat_id);
    
}

$parent_categories = $this->showParentCategoriesTree();
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'categorie.cancel')
        {
			
            Joomla.submitform(task, document.getElementById('ablog_comment_form'));
        }
        if (
                task == 'categorie.save' || task == 'categorie.saveEdit' ||
                task == 'categorie.saveReturn' || task == 'categorie.saveEditReturn'
                    
            ) {
            Joomla.submitform(task, document.getElementById('ablog_comment_form'));
        }
    }
</script>
<form action="index.php" method="post" name="adminForm" id="ablog_comment_form" class="form-validate form-horizontal">
            <table border="0">
                <tr>
                    <td>
                        <label for="categorie"><?php echo JText::_('COM_ABLOG_BLOGCATEGORIES_CATEGORY'); ?></label>  
                    </td>
                    <td>
                        <input type="text" id="categorie" name="title" id="categorie" size="25" value="<?php if ($task == 'edit') echo $categorie->title; ?>"/>
                    </td>
                </tr>   
                <tr>
                    <td>
                        <label for="published"><?php echo JText::_('COM_ABLOG_PUBLISHED'); ?></label>
                    </td>
                    <td class="radiobuttons">
                        <?php
                            if(isset($categorie->published) && $categorie->published == 1){
                                echo JHtml::_('select.booleanlist', 'published', 'class="radiobutton"','1');
                            }else{
                                echo JHtml::_('select.booleanlist', 'published', 'class="radiobutton"','0');
                            }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align: top;padding-right: 5px;">
                        <label for="parent"><?php echo JText::_('COM_ABLOG_PARENT'); ?></label>
                    </td>
                    <td>            
                        <select class="span11" name="parent_id">
                            <option value="0"><?php echo JText::_('COM_ABLOG_NO_PARENT');?></option>
                            <?php
                            //get all categories with parent_id
                            foreach ($parent_categories as $p_categorie) {
                                if ($p_categorie->id == 1 || $task == 'edit' && $p_categorie->id == $cat_id) {
                                    continue;
                                }
								//determine level of parent categorie
                                $p_categorie->level . "<br />";
                                $counter1 = $p_categorie->level;
                                $counter2 = $p_categorie->level;

                                $selected = '';
                               
								//determine if $parent_id with cat_id
                                if (isset($parent_id_from_cat_id)) {
                                	
                                    if ($task == 'edit' && $parent_id_from_cat_id->parent_id == $p_categorie->id) {
                                    	
                                        $selected = 'selected=selected';
                                        
                                    }
                                }
                                
                               

                                echo "<option " . $selected . " value=\"$p_categorie->id\">";

                                while ($counter2 > 1) {
                                    $leer = '&emsp;';
                                    echo $leer;
                                    $counter2--;
                                }


                                while ($counter1 > 1) {
                                    $leer = "#";
                                    echo $strich = "&thinsp;&ndash;";
                                    $strich;
                                    $counter1--;
                                }

                                echo $p_categorie->title . "</option>";
                            }
                            ?>         
                        </select>
                    </td>
                </tr>
            </table>
<?php echo JHtml::_('form.token');?>
  <input type="hidden" name="task" value="" />
            <input type="hidden" name="option" value="com_ablog" />
            <input type="hidden" name="act" value="categorie" />
            <input type="hidden" name="cid" value="<?php if($task == 'edit' && isset($categorie)) echo $categorie->id;?>" />
            <input type="hidden" name="hits" value="0" />
            <input type="hidden" name="id" value="<?php if ($task == 'edit' && isset($categorie)) echo $categorie->id; ?>" />
            </form>
          