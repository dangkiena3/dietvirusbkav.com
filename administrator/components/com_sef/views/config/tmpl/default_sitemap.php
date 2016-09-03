<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.6
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2016 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
defined('_JEXEC') or die('Restricted access');

echo JHtml::_('tabs.panel', JText::_('COM_SEF_SITEMAP'), 'sitemap');

JoomSEF::OnlyPaidVersion();
?>