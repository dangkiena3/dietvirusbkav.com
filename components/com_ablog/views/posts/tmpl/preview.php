<?php $this->preview_post = $this->getPostById(); $app = JFactory::getApplication();?>
<div id="ablog_content_post">
    <div id="preview_edit">Preview Edit:</div>
    <div class="editborder">
        <h2><?php
            if (isset($this->preview_post->title)) {
                echo $this->preview_post->title;
            }
            ?></h2>
        <p>Content:&nbsp;<?php
            if (isset($this->preview_post)) {
                echo $this->preview_post->content;
            }
            ?></p>
        <em>Published:&nbsp;<?php
            if (isset($this->preview_post) && $this->preview_post->published == 1) {
                echo 'Yes';
            } else {
                echo 'No';
            }
            ?></em>
    </div>
</div>
<form method="post" action="<?php echo JUri::base().'index.php?option=com_ablog&view=post&id=' . $app->input->getInt('id') . '&title='. $this->preview_post->title .'&Itemid=' . $app->getUserStateFromRequest('com_ablogItemid','Itemid'); ?>">
    <div class="btn-group">
        <button type="submit" class="btn btn-primary">
            <i class="icon-new"></i> <?php echo JText::_('COM_ABLOG_EDIT') ?>
        </button>
    </div>
    <input type="hidden" name="task" value="editlayout" />
<?php echo JHtml::_('form.token'); ?>
</form>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_ablog&view=post&id='. $app->input->getInt('id').'&title='.$this->preview_post->title .'&Itemid='. $app->getUserStateFromRequest('com_ablogItemid','Itemid')); ?>">  
    <input type="submit" value="Return to PostView" id="return_to_post"/>
</form>