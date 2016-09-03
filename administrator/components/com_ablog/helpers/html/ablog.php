<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('aBlogHelper', JPATH_ADMINISTRATOR . '/components/com_content/helpers/ablog.php');

/**
 * Content HTML helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @since       3.0
 */
abstract class JHtmlABlog
{
	/**
	 * Render the list of associated items
	 *
	 * @param   int  $articleid  The article item id
	 *
	 * @return  string  The language HTML
	 */
	public static function getTheAdminStyle()
        {
           return JHtml::_('stylesheet','administrator/components/com_ablog/assets/css/admin.css');
        }
}
