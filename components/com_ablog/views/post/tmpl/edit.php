<?php
$app = JFactory::getApplication();
$editor = JFactory::getEditor();
jimport('joomla.html.html');
$this->post = $this->result_post;
$this->categorie_name = $this->getCategorieNameByCategorieId($this->post->categorie_id)->title;
?>
<div id="ablog_content_post">
    <form method="post" action="<?php echo JUri::base().'index.php?option=com_ablog&view=post&task=savepost&id=' . $app->input->getInt('id') . '&title='. $this->post->title .'&Itemid=' . $app->input->getInt('Itemid'); ?>">
        <h2><?php echo JText::_('COM_ABLOG_EDIT'); ?></h2>
        <label for="title">title</label>
        <input type="text" name="title_edit" id="title" value="<?php echo $this->post->title; ?>"/>
        <input type="text"  value="<?php echo $this->categorie_name; ?>" readonly="readonly" />
        <?php
        $user = JFactory::getUser();
        echo $editor->display('ablog_front_content',$this->result_post->content, '100%', 400, 100, 100, false,'ablog_content');
        $current_date = JFactory::getDate(null, JFactory::getConfig()->get('offset'));
        $current_date = $current_date->__toString();
        ?>
        <div class="hidden-phone clearfloat">
            <strong id="start_publishing"><?php echo JText::_("COM_ABLOG_START_PUBLISHING");?></strong><br />

            <?php echo JHtml::_('calendar', $current_date, 'publish_start', 'publish_start','%Y-%m-%d %H:%M:%S'); ?><br />

            <strong id="end_publishing"><?php echo JText::_("COM_ABLOG_END_PUBLISHING");?></strong><br />
            <?php echo JHtml::_('calendar', $current_date, 'publish_stop', 'publish_stop','%Y-%m-%d %H:%M:%S'); ?><br />
        </div>
<div id="published_options">
<?php
 $published = $this->post->published;
?>
    <input type="radio" name="published" value="0" <?php if($published){echo 'checked="checked"';}?>/>No<br>
    <input type="radio" name="published" value="1" <?php if($published){echo 'checked="checked"';}?> />Yes<br>
</div>
<input type="hidden" name="edit_kategorie" value="<?php echo $this->post->categorie_id; ?>" />
<input type="submit" value="<?php echo JText::_('COM_ABLOG_SAVE'); ?>" onclick="return validateForm();" id="save"/>
<?php echo JHtml::_('form.token'); ?>
</form>
</div>
<form method="post" action="index.php?option=com_ablog&view=post&task=post&id=<?php echo $app->input->getInt('id') . '&title='.$this->post->title; ?>">  
    <input type="submit" value="<?php echo JText::_('COM_ABLOG_RETURN_TO_POSTVIEW'); ?>" id="return_to_post"/>
</form>
<script type="text/javascript">

    function validateForm() {
        //max amount failures
        //get the input and output fields
        var error_message = '';
        var content_iframe = tinyMCE.activeEditor.getContent();
        var title = document.getElementById('title').value;
        //remove br tag and p tags
        if (title == "" || title == null) {
            error_message += 'Please enter the creator field\n';
        }

        if (content_iframe == "" || content_iframe == null) {
            error_message += 'Please enter the content field';
        }

        if (error_message.length > 0) {
            return false;
        }
    }

    var start = jQuery('#publish_start_img i').replaceWith('<i class="ablog-calendar"></i>');
	var stop = jQuery('#publish_stop_img i').replaceWith('<i class="ablog-calendar"></i>');
</script>