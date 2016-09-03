<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$params 	= JFactory::getApplication()->getTemplate(true)->params;

$format = $displayData;

if($params->get('show_post_format')) {

	echo '<span class="post-format">';

	if (  $format == 'audio' ) {
		echo '<i class="icon-mic"></i>';
	} else if (  $format == 'video' ) {
		echo '<i class="icon-video"></i>';
	} else if (  $format == 'gallery' ) {
		echo '<i class="icon-pictures"></i>';
	} else if (  $format == 'quote' ) {
		echo '<i class="icon-quote"></i>';
	} else if (  $format == 'link' ) {
		echo '<i class="fa fa-link"></i>';
	} else if (  $format == 'status' ) {
		echo '<i class="icon-chat"></i>';
	} else {
		echo '<i class="icon-pencil"></i>';
	}

	echo '</span>';

}