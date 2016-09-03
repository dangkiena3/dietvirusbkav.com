<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_contact
 *
 * @package     Joomla.Site
 * @subpackage  com_contact
 * @since       3.3
 */
class aBlogRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_ablog component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */	
	
	public function build(&$query)
	{
		
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menu->getActive();		
		
		$segments = array();
		
		if(isset($query)){
			//var_dump($query); 
		}
		
		
		if(count($query) == 6 && $query['view'] == 'post'){
			if(isset($query['view'])){
				$segments[]=$query['id'].':'.$query['view'];
				unset($query['view']);
				unset($query['id']);
			}
			
			if(isset($query['title'])){
				$segments[] = $query['title'];
				unset($query['title']);
			}
			
			if(isset($query['cat'])){
				$segments[] = $query['cat'];
				unset($query['cat']);
			}
			
			$segments[0] = str_replace(':', '-', $segments[0]);
		}
		
		if(count($query) == 4){
			if(isset($query['view'])){
				$segments[]=$query['view'];
				unset($query['view']);
			}
			if(isset($query['id'])){
				$segments[]=$query['id'];
				unset($query['id']);
			}	
		}
		
		if(count($query) == 5 && $query['view'] != 'posts'){
			if(isset($query['view'])  && isset($query['id'])){
				$segments[]=$query['id'].':'.$query['view'];
				unset($query['view']);
				unset($query['id']);
			}
			
			if(isset($query['title'])){
				$segments[]=$query['title'];
				unset($query['title']);
			}
			
			$segments[0] = str_replace(':', '-', $segments[0]);
		}
		
		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		
		
		$vars = array();
		
		
		if(isset($segments) && count($segments) == 3){
			
			$pos = strpos($segments[0],'-');
			
			$segments[0] = str_replace('-', ':', $segments[0]);
				
			list($id,$view) = explode(':', $segments[0], 2);
			
			$vars['view']=$view;
			$vars['id']=$id;
			$vars['title']=$segments[1];
			$vars['cat']=$segments[2];
		}
		
		
		if(isset($segments) && count($segments)==2){
			$vars['view']=$segments[0];
			$vars['id']=$segments[1];
		}
		
	    $pos = strpos($segments[0],'-');
		
		if(isset($segments) && count($segments)==2 && $pos){
			
			
			$segments[0] = str_replace('-', ':', $segments[0]);
			
			list($id,$view) = explode(':', $segments[0], 2);
			
			
			$vars['view']=$view;
			$vars['id']=$id;
			$vars['title']=$segments[1];
		}
		
		
			return $vars;

		}

/**
 * Contact router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
	function aBlogBuildRoute(&$query)
	{
		$router = new aBlogRouter;
	
		return $router->build($query);
	}
	
	function aBlogParseRoute($segments)
	{
		$router = new aBlogRouter;
	
		return $router->parse($segments);
	}
}