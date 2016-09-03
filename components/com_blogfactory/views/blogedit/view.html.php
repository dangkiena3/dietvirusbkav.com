<?php

/**
-------------------------------------------------------------------------
blogfactory - Blog Factory 4.2.5
-------------------------------------------------------------------------
 * @author thePHPfactory
 * @copyright Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.thePHPfactory.com
 * Technical Support: Forum - http://www.thePHPfactory.com/forum/
-------------------------------------------------------------------------
*/

defined('_JEXEC') or die;

class BlogFactoryFrontendViewBlogEdit extends BlogFactoryFrontendView
{
  protected
    $variables = array('form', 'item'),
    $jhtmls = array('behavior.tooltip', 'jquery.framework', 'formbehavior.chosen/select')
  ;

  protected function renderBox($box)
  {
    $this->box = $box;

    return $this->loadTemplate('box');
  }
}
