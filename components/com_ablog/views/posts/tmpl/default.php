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
$params = JComponentHelper::getParams('com_ablog');
$menu_round_corners = $params->get('ablog_menu_round_corners');
$menu_color = $params->get('ablog_menu_color');
$menu_textcolor = $params->get('ablog_menu_text_color');
$menu_text_size = $params->get('ablog_menu_fontsize');
$menu_text_weight = $params->get('ablog_menu_text_weight');
$menu_height = $params->get('ablog_menu_height');
$menu_border_weight = $params->get('ablog_menu_border_weight');
$menu_border_color = $params->get('ablog_menu_border_color');
$menu_border_style = $params->get('ablog_menu_border_style');
$menu_border_radius = $params->get('ablog_menu_border_radius');
$menu_buttons_margin = $params->get('ablog_menu_li_margin');
$menu_buttons_padding = $params->get('ablog_menu_li_padding');
$menu_fontsize = $params->get('ablog_menu_fontsize');
$menu_width = $params->get('ablog_menu_width');
$menu_active_button_color = $params->get('ablog_menu_active_color');
$media_image = $params->get('ablog_menu_image');
$menu_image_width = $params->get('ablog_menu_image_width');
$menu_sublink_active_background = $params->get('ablog_menu_active_sublink_color');
$menu_sublink_background = $params->get('ablog_menu_sublink_color');

$posts_text_color = $params->get('ablog_text_color');
$posts_ablog_border_weight = $params->get('ablog_posts_border_weight');
$posts_ablog_border_style = $params->get('ablog_posts_border_style');
$posts_ablog_border_color = $params->get('ablog_posts_border_color');
$posts_ablog_background = $params->get('ablog_posts_background');
$posts_ablog_padding = $params->get('ablog_posts_padding');
$posts_ablog_border_radius = $params->get('ablog_posts_border_radius');
$posts_ablog_width = $params->get('ablog_posts_width');
// $posts_ablog_social_border_radius = $params->get('ablog_social_border_radius');
$posts_ablog_social_background_color = $params->get('ablog_social_background_color');
$posts_ablog_social_border_weight = $params->get('ablog_social_border_weight');
$posts_ablog_social_border_style = $params->get('ablog_social_border_style');
$posts_ablog_social_border_color = $params->get('ablog_social_border_color');
$posts_ablog_social_padding = $params->get('ablog_social_padding');
$posts_ablog_social_border_radius = $params->get('ablog_social_border_radius');
// Params to change the Facebook and Twitter links pointing
$show_author = $params->get('publishing_author');
$show_created_date = $params->get('publishing_date');
$from_string = JText::_('COM_ABLOG_POSTS_FROM') . ':';
$show_posts_on = JText::_('COM_ABLOG_POSTS_ON') . ':';

($show_author == 1) ? $from_string : $from_string = '';
($show_created_date == 1) ? $show_posts_on : $show_posts_on = '';

if ($menu_text_weight == '1') {
    $text_weight = 'bold';
} else {
    $text_weight = 'normal';
}

if ($media_image != '') {
    $image_path = JUri::base();
    
    $image_path = $media_image = '#main_ablog #menu_image_ul #menu_image a{
                       background: url(' . $image_path . $media_image . ') no-repeat center;
                       height:' . $menu_height . ';' . 'width:' . '60px' . ';' . '}';
} else {
    $media_image = '';
}
$content_background = $params->get('ablog_content_background');
$content_color = $params->get('ablog_text_color');
$content_h2_color_link = $params->get('ablog_h2_color_link');
$content_h2_color_hover = $params->get('ablog_h2_color_hover');
$content_h2_hover_background = $params->get('ablog_h2_hover_background_color');
$document = JFactory::getDocument();
$styles_menu = '#main_ablog ul#ablog_menu li:first-child a{
             -moz-border-radius-bottomleft:' . $menu_round_corners . ';' . '-webkit-border-bottom-left-radius:' . $menu_round_corners . ';' . '-khtml-border-bottom-left-radius:' . $menu_round_corners . ';' . 'border-bottom-left-radius:' . $menu_round_corners . ';' . '-moz-border-radius-topleft:' . $menu_round_corners . ';' . '-webkit-border-top-left-radius:' . $menu_round_corners . ';' . '-khtml-border-top-left-radius:' . $menu_round_corners . ';' . 'border-top-left-radius:' . $menu_round_corners . ';' . '}' . '#main_ablog ul#ablog_menu li:last-child a{
             -moz-border-radius-bottomright:' . $menu_round_corners . ';' . '-webkit-border-bottom-right-radius:' . $menu_round_corners . ';' . '-khtml-border-bottom-right-radius:' . $menu_round_corners . ';' . 'border-bottom-right-radius:' . $menu_round_corners . ';' . '-moz-border-radius-topright:' . $menu_round_corners . ';' . '-webkit-border-top-right-radius:' . $menu_round_corners . ';' . '-khtml-border-top-right-radius:' . $menu_round_corners . ';' . 'border-top-right-radius:' . $menu_round_corners . ';' . '}' . '#main_ablog ul#ablog_menu li a{                
                    width:' . $menu_width . ';' . 'color:' . $menu_textcolor . ';' . 'font-weight:' . $text_weight . ';' . 'text-decoration: none;' . 'background-color:' . $menu_color . ';' . 'padding: ' . $menu_buttons_padding . ';' . 'margin-right: ' . $menu_buttons_margin . ';' . 'font-size:' . $menu_fontsize . ';' . 'height:' . $menu_height . ';' . 'line-height: ' . $menu_height . ';' . 'text-align: left' . ';' . 'border-left-width:0;' . 'border-top-width:' . $menu_border_weight . ';' . 'border-bottom-width:' . $menu_border_weight . ';' . 'border-style:' . $menu_border_style . ';' . 'border-color:' . $menu_border_color . ';' . '}' . '#main_ablog ul#ablog_menu li a{
            border-right-width:' . $menu_border_weight . ';' . 'border-style:' . $menu_border_style . ';' . 'border-color:' . $menu_border_color . ';' . '}' . '#main_ablog ul#ablog_menu li:last-child a{
            border-right-width:' . $menu_border_weight . ';' . 'border-style:' . $menu_border_style . ';' . 'border-color:' . $menu_border_color . ';' . '}' . '#main_ablog ul#ablog_menu li ul li:last-child a{
        border-left-width:' . $menu_border_weight . ';' . 'border-style:' . $menu_border_style . ';' . 'border-color:' . $menu_border_color . ';' . 'background:' . $menu_sublink_background . ';' . '}' . 'ul#ablog_menu li#active_sublink{
            background-color:' . $menu_sublink_active_background . ';' . '}' . '#main_ablog ul#ablog_menu li.menu_image a#menu_image{
         border-left-width:' . $menu_border_weight . ';' . 'border-top-width:' . $menu_border_weight . ';' . 'border-bottom-width:' . $menu_border_weight . ';' . 'border-right-width:' . $menu_border_weight . ';' . 'border-style:' . $menu_border_style . ';' . 'border-color:' . $menu_border_color . ';' . '}' . 'ul#ablog_menu li ul li a.sublinks{
            background-color: .' . $menu_sublink_background . ';
         }' . '#main_ablog ul#ablog_menu li {
                    height:' . $menu_height . '}' . '#main_ablog ul#ablog_menu li a#active_menu{
         background-color:' . $menu_active_button_color . ';' . '}';
$styles_content = '#main_ablog .ablog_content_container .main {' . 'background:' . $posts_ablog_background . ';' . 'border-width:' . $posts_ablog_border_weight . ';' . 'border-style:' . $posts_ablog_border_style . ';' . 'border-color:' . $posts_ablog_border_color . ';' . 'border-radius:' . $posts_ablog_border_radius . ';' . 'padding:' . $posts_ablog_padding . ';' . 'width:' . $posts_ablog_width . ';' . '}' . '#main_ablog div.main{' . 'color:' . $posts_text_color . ';' . '}' . '#main_ablog div.main h2 a
                   {
                        color:' . $content_h2_color_link . ';' . '}' . '#main_ablog div.main h2 a:link,
                   #main_container_ablog .main_ablog .main h2 a:visited {
                        color:' . $content_h2_color_link . ';' . '}' . '#main_ablog div.main h2 a:hover,
                    #main_ablog .main_ablog .main h2 a:active {
                        background: ' . $content_h2_hover_background . ';' . 'color:' . $content_h2_color_hover . ';' . '}'.
                    '#facebook_button{ padding-top:5px;}';
$styles_social_media = '#main_ablog .ablog_content_container .social_media {' . 'border-width:' . $posts_ablog_social_border_weight . ';' . 'border-style:' . $posts_ablog_social_border_style . ';' . 'border-color:' . $posts_ablog_social_border_color . ';' . 'padding:' . $posts_ablog_social_padding . ';' . 'background:' . $posts_ablog_social_background_color . ';' . 'border-radius:' . $posts_ablog_social_border_radius . ';' . '}';
$styles = $styles_menu . ' ' . $styles_content . ' ' . $styles_social_media . ' ' . $media_image;
$document->addStyleDeclaration($styles);
$option = 'com_ablog';
$item_id_state = $app->getUserStateFromRequest($option . 'Itemid', 'Itemid', '');

if ($this->checkAuthorise()) {
    ?>
<form method="post"
	action="<?php echo JUri::base().'index.php?option=com_ablog&view=posts&task=editlayout&Itemid='.$item_id_state; ?>">
	<div class="btn-group">
		<button type="submit" class="btn btn-primary" id="add_post_button">
			<i class="icon-new"></i> <?php echo JText::_('COM_ABLOG_ADD_POST')?>
            </button>
	</div>
    <?php echo JHtml::_('form.token'); ?>
    </form>
<form method="post"
	action="<?php echo JUri::base().'index.php?option=com_ablog&view=posts&task=seeall&Itemid=' . $item_id_state; ?>">
	<div class="btn-group">
		<button type="submit" class="btn btn-primary" id="see_all_button">
			<i class="icon-new"></i> <?php echo JText::_('COM_ABLLOG_POSTS_SEE_ALL'); ?>
            </button>
	</div>
    <?php echo JHtml::_('form.token'); ?>
    </form>
<?php } ?>
<div id="main_ablog">
	<ul id="menu_image_ul">
		<li id="menu_image"><a class="no_border"
href="<?php echo JRoute::_('index.php?option=com_ablog&Itemid='.$item_id_state); ?>"></a>
		</li>
	</ul>
	<ul id="ablog_menu">               
<?php echo $this->mainCategories(); ?>
    </ul>
	<div
		style="display: block; clear: both; height: 0; visibility: collapse;">&nbsp;</div>



	<br style="clear: both; height: 0;" /> <input type="hidden"
		name="cat_id" value="" />
		
<?php
if (isset($this->posts)) : 
    foreach ($this->posts as $post) :
?>
	<div class="ablog_content_container">
		<div class="social_media">		
    		<?php //aBlogHelper::facebookLikeButton($post,$app,$option);?>
            <?php //aBlogHelper::twitterButton($post,$app,$option);?>
    	    <?php //aBlogHelper::googleButton($post,$app,$option);?>
		</div>		
        <?php
        ($show_author == 1) ? $post->creator_username : $post->creator_username = '';
        ($show_created_date == 1) ? $post->created_date : $post->created_date = '';
        ?>
                <div class="main">
			<h2>
				<a
					href="<?php echo JRoute::_('index.php?option=com_ablog&view=post&id=' . $post->id . '&title='. strtolower($post->title) .'&Itemid='. $app->getUserStateFromRequest($option . 'Itemid', 'Itemid')); ?>"><?php echo $post->title; ?></a>
			</h2>
			<p class="first_main_p"><?php echo $from_string . '  ' . $post->creator_username . ' ' . $show_posts_on . '  ' . $post->created_date; ?></p>
			<div><?php echo $post->content; ?></div>
		</div>
		<br style='clear: both; height: 0;' />
	</div>
	<!--ablog content container-->
        <?php
    endforeach
    ;
endif;

?>
    <div id="ablog_pagination">
<?php echo $this->getPaginationPosts(); ?>
    </div>
	<!--end pagination-->
	<noscript>Javascript should be on for the full functionality</noscript>
</div>
<!--end mainblog-->
