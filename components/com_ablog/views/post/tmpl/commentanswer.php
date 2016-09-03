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
$app->input->get('Itemid');
$app->input->get('id');
$option = 'com_ablog';
$editor = JFactory::getEditor();
//Params for Styles
$params = JComponentHelper::getParams('com_ablog');
//post container
$container_padding = $params->get("ablog_post_container_padding");
$container_border = $params->get("ablog_post_container_border");
$container_corners = $params->get("ablog_post_container_corners");
$title_color = $params->get("ablog_post_title_color");
$title_size = $params->get("ablog_post_title_size");
$text_color = $params->get("ablog_post_text_color");
$text_size = $params->get("ablog_post_text_size");
$text_weight = $params->get("ablog_post_text_weight");
//comment container
$comment_text_color = $params->get("ablog_post_comment_text_color");
$comment_text_size = $params->get("ablog_post_comment_text_size");
$comment_line_color = $params->get("ablog_post_comment_line_color");

//form styles
$form_background_color = $params->get('ablog_form_background');
$form_message1_color = $params->get('ablog_form_message1_color');
$form_message1_font_size = $params->get('ablog_form_message1_font_size');
$form_message2_color = $params->get('ablog_form_message2_color');
$form_message2_font_size = $params->get('ablog_form_message2_font_size');
$form_color_label = $params->get('ablog_form_color_label');
$form_label_font_size = $params->get('ablog_form_font_size_label');
$form_input_focus = $params->get('ablog_form_input_focus');
$form_button_color = $params->get('ablog_form_button_color');
$form_button_font_size = $params->get('ablog_form_button_font_size');
$form_button_background_color = $params->get('ablog_form_button_background_color');

$post_button_background_color = $params->get('ablog_post_button_background_color');
$post_button_font_size = $params->get('ablog_post_button_font_size');
$post_button_color = $params->get('ablog_post_button_color');

$post_styles = "div#post{
                    padding:" . $container_padding . ";" .
        "color:" . $text_color . ";" .
        "border:" . $container_border . " solid black;" .
        "border-radius:" . $container_corners . ";" .
        "font-size:" . $text_size . ";" .
        "font-weight:" . $text_weight .
        "}" .
        "div#post h2{
                    color:" . $title_color . ";" .
        "font-size:" . $title_size . ";" .
        ";}" .
        $comment_styles = "div.comments{
                       color:" . $comment_text_color . ";" .
        "font-size:" . $comment_text_size . ";" .
        "}" .
        "p.answer a{
                       color:" . $comment_text_color . ";" .
        "}" .
        ".content, .comments_inner{
                       border-color:" . $comment_line_color . ";" .
        "}" .
         "div#post #back_button{
            background:". $post_button_background_color . ";" .
            "font-size:"    . $post_button_font_size . ";" .
            "color:" . $post_button_color . ";" .
            "line-height: normal !important".
            ";}";       

$form_styles = "#ablog_content_post #form{
                    background:" . $form_background_color . ";" .
        "}" .
        "#form strong{
                    color:" . $form_message1_color . ";" .
        "font-size:" . $form_message1_font_size . ";" .
        "}" .
        "#form em{
                    color:" . $form_message2_color . ";" .
        "font_size:" . $form_message2_font_size . ";" .
        "}" .
        "  #form label[for='creator'],
        #form label[for='email_adress'],
        #form label[for='content'] {
            color: " . $form_color_label. ";
            font-size: ". $form_label_font_size.
        "}"
        .
        "#form input:focus, #form textarea:focus{
                    border:" . $form_input_focus . ";" .
        "}" .
        "#postform #submit_button{
            background:". $form_button_background_color . ";" .
            "font-size:". $form_button_font_size . ";" .
            "color:" . $form_button_color . ";".
            "line-height: normal; !important;" .
        "}";

$styles = $post_styles . ' ' . $comment_styles . ' ' . $form_styles;
$date = new JDate();
$document = JFactory::getDocument();
$document->addStyleDeclaration($styles);
$params = JComponentHelper::getParams('com_ablog');
$posts_ablog_created_date = $params->get('ablog_posts_created_date');


if ($this->canEdit($this->result_post->checked_out)) {
    ?>
    <form method="post" action="<?php echo JRoute::_('index.php?option=com_ablog&view=post&id=' . $app->input->getInt('id') . '&Itemid=' . $app->getUserStateFromRequest('com_ablog' . 'Itemid', 'Itemid')); ?>">
        <div class="btn-group">
            <button id="edit_button" type="submit" class="btn btn-primary">
                <i class="icon-new"></i> <?php echo JText::_('Edit'); ?>
            </button>
        </div>
        <input type="hidden" name="task" value="editlayout" />
    </form>
<?php } ?>
<div id="ablog_content_post">
    <div class="social_media">
        <?php //aBlogHelper::facebookLikeButton($this->result_post, $app, $option);?>
        <?php //aBlogHelper::twitterButton($this->result_post, $app, $option);?>
        <?php //aBlogHelper::googleButton($this->result_post, $app, $option);?> 
    </div>

    <div id="post">    
        <h2><?php echo $this->result_post->title; ?></h2>
        <p><?php
            if ($posts_ablog_created_date) {
                echo JText::_('COM_ABLOG_POST_FROM') . ': ' . $this->result_post->creator . JText::_('COM_ABLOG_POST_ON') . ': ' . $this->result_post->created_date . $this->showHits();
            }
            ?></p>
        <div><?php echo $this->result_post->content; ?></div>
        <form method="post" action="<?php echo JRoute::_('index.php?option=com_ablog&view=posts&Itemid=' . $app->getUserStateFromRequest('com_ablogItemid', 'Itemid')); ?>">
            <button id="back_button" style="background: <?php echo $form_button_background_color ?>" >Back</button>
        </form>
    </div>
    <?php

    $i = 1;
    if ($this->result_comments) {
        ?>
        <?php foreach ($this->result_comments as $comment) {
            ?>
            <div class="comments">
                <h3><?php echo $i++ . ' ' . JText::_('COM_ABLOG_POST_COMMENTS') . ' ' . JText::_('COM_ABLOG_POST_FOR') . ' ' . strtolower($this->result_post->title); ?></h3>
                <div class="comments_inner">
                    <div class="personal_data">
                        <p><?php echo $this->cleanInput($comment->creator); ?></p>
                        <p><?php echo $comment->created_date; ?></p>
                    </div>
                    <div>
                        <div><?php echo aBlogHelper::purifyHtmlCode($comment->content); ?></div>
                        <p class="answer"><a href="<?php echo JRoute::_('index.php?option=com_ablog&view=post&id=' . $app->input->getCmd('id') . '&cat=' . $comment->id . '&Itemid=' . $app->getUserStateFromRequest('com_ablogItemid' . 'Itemid', 'Itemid')); ?>#form_position" >Answer</a></p>
                    </div>
                </div>
                <?php if ($this->getCommentAnswersForView($this->result_post->id, $comment->id)) { ?>
                    <?php foreach ($this->getCommentAnswersForView($this->result_post->id, $comment->id) as $comment_answer) { ?>    
                        <div class="comment_answers">
                            <div class="personal_data">
                                <p><?php echo $this->cleanInput($comment_answer->creator); ?></p>
                                <p><?php echo $this->cleanInput($comment_answer->created_date); ?></p>
                            </div>                   
                            <p><?php echo aBlogHelper::purifyHtmlCode($comment_answer->content); ?></p>
                            <br class="clearfloat" />
                        </div>    
                    <?php } ?>
                <?php } ?>
            </div>  
            <?php
        }
    }
    ?>
    <div id="form">       
        <form method="post" action="<?php echo JUri::base() . 'index.php?option=com_ablog&view=post&id=' . $app->getUserStateFromRequest('com_ablogid', 'id') . '&title='.$app->input->getString('title').'&cat='.$app->input->getInt('cat').'&Itemid=' . $app->getUserStateFromRequest('com_ablogItemid', 'Itemid'); ?>" id="postform" name="adminForm">  

            <table id="form_table" class="table table-striped">
                <tr>
                    <td>
                        <strong id="form_position"><?php echo JText::_('COM_ABLOG_POST_LEAVE_A_MESSAGE'); ?></strong>
                        <p><em><?php echo JText::_('COM_ABLOG_EMAIL_NOT_PUBLISHED'); ?></em></p>
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <label for="creator">Creator</label>
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <input type="text" id="creator" name="creator" />
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <label for="email_adress">EmailAdress</label>
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <input type="text" id="email_adress" name="email_adress" />
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <label for="content">Content</label>
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <?php echo str_replace(array("\r\n","\r\n\r\n"), array('<br />','<p>'), aBlogHelper::aBlogEditorTiny());?>
                    </td>
                </tr>
                <tr>
                    <td class="td_class">
                        <?php
                         	$this->getCaptchaDiv();                            
                        ?>
                         <div id="dynamic_recaptcha_1"></div>
                        <div class="form_errors"><?php echo $this->form_errors; ?></div>
                        <button id="submit_button" type="submit" onclick="return validateForm();"><?php echo JText::_("COM_ABLOG_SAVE_COMMENT_ANSWERS");?></button>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="created_date" value="<?php echo $date->toSql(); ?>" />
            <input type="hidden" name="post_id" value="<?php echo $this->result_post->id; ?>" />
            <input type="hidden" name="id" value="<?php echo $this->result_post->id; ?>" />
            <input type="hidden" name="task" value="save_comment_answers" />
            <input type="hidden" name="comment_id" value="<?php echo $app->input->get('cat','','int');?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
           <script type="text/javascript">
                            
                            var content_iframe = "";
                                
                            function validateForm() {
                            	 var error_message = "";
                                 //get input and ouput fields                               
                                 var content_iframe = tinyMCE.activeEditor.getContent();
                                 var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                         		
                                 //var br_tag = the_iframe.contentDocument.body.getElementsByTagName("br")[0];
                                 var creator = document.getElementById('creator').value;
                                 var email_adress = document.getElementById('email_adress').value;

                                 if(document.getElementById('recaptcha_response_field') != null || 
                                    	document.getElementById('recaptcha_response_field') != 'undefined'){
 										captcha_field = document.getElementById('recaptcha_response_field');
                                 };

                              
                                   
                                  if (creator == "" || creator == null) {
                                      error_message += 'Please enter the creator field\n';
                                  }
                                    

								   if(!re.test(email_adress)){
										error_message += 'Please enter the email_field';
								   }
                                    
                                   if(content_iframe == ""){
                                        error_message += 'Please enter the content field';
                                   }

                                    if(captcha_field){                                       
										if(captcha_field.value == ""){
											error_message += 'Please enter the content field';
										}
                                    }
                                    
                                    if(content_iframe.length >= 1000) alert("Please enter less that 1000 signs");
                                    
                                    if(error_message.length > 0){
                                        return false;
                                    } 
                            }
</script>
