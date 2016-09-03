<?php
$app = JFactory::getApplication();
$editor = JFactory::getEditor();
jimport('joomla.html.html');
?>
<div id="ablog_content_post">
    <form method="post" action="<?php echo JUri::base().'index.php?option=com_ablog&view=posts&task=savepost&Itemid=' . $app->input->getInt('Itemid'); ?>">
        <h2><?php echo JText::_("COM_ABLOG_CREATE_A_POST"); ?></h2>
        <label for="title"><?php echo JText::_("COM_ABLOG_POSTS_TITLE"); ?></label>
        <input type="text" name="title_edit" id="title" value="<?php echo $app->input->get('title_edit');?>"/>
        <select name="edit_kategorie">
            <?php foreach($this->categories as $categorie){ ?>
            <option value="<?php echo $categorie->id;?>">
                <?php echo $categorie->title; ?>
            </option>
            <?php } ?>
        </select>
        <?php
        $user = JFactory::getUser();
        echo $editor->display('ablog_front_content',$app->input->get('ablog_front_content'), '100%', 400, 100, 100, false,'ablog_content');
        $current_date = JFactory::getDate('',JFactory::getConfig()->get('offset'));
        $current_date = $current_date->__toString();
        ?>
        <div class="hidden-phone clearfloat">
        <strong id="start_publishing"><?php echo JText::_("COM_ABLOG_START_PUBLISHING");?></strong><br />
            <?php echo JHtml::_('calendar', $current_date, 'publish_start', 'publish_start','%Y-%m-%d %H:%M:%S'); ?><br />
        <strong id="end_publishing"><?php echo JText::_("COM_ABLOG_END_PUBLISHING");?></strong><br />
        <?php echo JHtml::_('calendar', $current_date, 'publish_stop', 'publish_stop','%Y-%m-%d %H:%M:%S'); ?><br />
        </div>

<div id="published_options">
    <input type="radio" name="published" value="0" /><?php echo JText::_("COM_ABLOG_NO"); ?><br>
    <input type="radio" name="published" value="1" /><?php echo JText::_("COM_ABLOG_YES"); ?><br>
</div>
<div class="form_errors"><?php echo $this->form_errors; ?></div>
<input type="submit" value="<?php echo JText::_("COM_ABLOG_SAVE");?>" onclick="return validateForm();" id="save"/>
<?php echo JHtml::_('form.token'); ?>
</form>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_ablog&view=posts&Itemid=' . $app->input->get('Itemid')); ?>">  
    <input type="submit" value="<?php echo JText::_("RETURN_TO_POSTS_VIEW");?>" id="return_to_posts"/>
</form>
</div>
<script type="text/javascript">                                
                            function validateForm() {                               
                                //get the input and output fields
                                var error_message = '';
                                var content_iframe = tinyMCE.activeEditor.getContent(); 
                                var title = document.getElementById('title').value;
                                //remove the br and p tags
                                
                                    if (title == "" || title == null) {
                                        error_message += 'Please enter the creator field\n';
                                    }
                               
                                    if(content_iframe == "" || content_iframe == null){
                                        error_message += 'Please enter the content field';
                                    }
                                    
                                    if(error_message.length > 0){
                                        return false;
                                    }                                   
                                    
                            }

                           
                            	var start = jQuery('#publish_start_img i').replaceWith('<i class="ablog-calendar"></i>');
                            	var stop = jQuery('#publish_stop_img i').replaceWith('<i class="ablog-calendar"></i>');
                            	
                            
                          

                        
</script>
