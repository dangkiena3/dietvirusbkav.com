<?php
$this->post = $this->getPostById();
$app = JFactory::getApplication();
?>
<div id="ablog_content_post">

    <div id="preview_edit">Preview Edit:</div>
    <div id="editpreview_content">
        <h2><?php if (isset($this->post)) {
    echo $this->post->title;
} ?></h2>
        <p>Content:&nbsp; <?php if (isset( $this->post)) {
    echo $this->post->content;
} ?></p>
        <em>Published:&nbsp;<?php
            if (isset($this->post) && $this->post->published == 1) {
                echo 'Yes';
            } else {
                echo 'No';
            }
?></em>
    </div>
    <form method="post" action="<?php echo JUri::base().'index.php?option=com_ablog&view=post&id=' . $app->input->getInt('id') . '&Itemid=' . $app->getUserStateFromRequest('com_ablogItemid', 'Itemid'); ?>">
        <div class="btn-group">
            <button type="submit" class="btn btn-primary" id="edit_button">
                <i class="icon-new"></i> <?php echo JText::_('COM_ABLOG_POST_EDIT') ?>
            </button>
        </div>
        <input type="hidden" name="task" value="editlayout" />
    </form>
    <form method="post" action="<?php echo JRoute::_('index.php?option=com_ablog&view=post&id=' . $app->input->getInt('id') . '&Itemid=' . $app->getUserStateFromRequest('com_ablogItemid', 'Itemid')); ?>">
        <div class="btn-group">
            <button type="submit" class="btn btn-primary" id="go_to_post_button">
                <i class="icon-new"></i> <?php echo JText::_('COM_ABLOG_GO_TO_POST') ?>
            </button>
        </div>
        <input type="hidden" name="task" value="shownotpublished" />
    </form>
</div>