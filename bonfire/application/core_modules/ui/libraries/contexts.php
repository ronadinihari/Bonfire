<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	Class: Contexts
	
	Provides helper methods for displaying Context Navigation.
*/
class Contexts {

	/*
		Var: $actions
		Stores the available menu actions.
	*/
	protected static $actions = array();
	
	/*
		Var: $menu
		Stores the organized menu actions.
	*/
	protected static $menu	= array();

	/*
		Var: $ci
		Pointer to the CodeIgniter instance.
		
		Access:
			Protected
	*/
	protected static $ci;

	//--------------------------------------------------------------------

	public function __construct() 
	{
		self::$ci =& get_instance();
		self::init();
	}
	
	//--------------------------------------------------------------------
	
	protected static function init() 
	{
		if (!function_exists('module_list'))
		{
			self::$ci->load->helper('application');
		}
	
		log_message('debug', 'UI/Contexts library loaded');
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: render_menu()
		
		Renders a list-based menu (with submenus) for each context.
	*/
	public static function render_menu($mode='icon')
	{
		self::$ci->benchmark->mark('context_menu_start');
	
		$contexts = self::$ci->config->item('contexts');
	
		if (empty($contexts) || !is_array($contexts) || !count($contexts))
		{
			die(lang('bf_no_contexts'));
		}
		
		// Ensure settings context exists
		if (!in_array('settings', $contexts))
		{
			array_push($contexts, 'settings');
		}
		
		// Ensure developer context exists
		if (!in_array('developer', $contexts))
		{
			array_push($contexts, 'developer');
		}
	
		$nav = '';

		/*
			Build out our navigation.
		*/
		foreach ($contexts as $context)
		{	
			if (has_permission('Site.'. ucfirst($context) .'.View'))
			{	
				$url = site_url(SITE_AREA .'/'.$context);
				$class = check_class($context, true);
				$id = 'tb_'. $context;
				$title = lang('bf_context_'. $context);
				
				$nav .= "<li class='dropdown {$class}'><a href='{$url}' id='{$id}' class='dropdown-toggle' title='{$title}'>";
				
				// Image
				if ($mode=='icon' || $mode=='both')
				{
					$nav .= "<img src='". Template::theme_url('images/context_'. $context .'.png') ."' alt='{$title}' />"; 
				}
				
				// Display String
				if ($mode=='text' || $mode=='both')
				{
					$nav .= $title;
				}
				
				$nav .= "</a>";
				
				$nav .= self::context_nav($context);
				
				$nav .= "</li>";
			}
		}
		
		self::$ci->benchmark->mark('context_menu_end');
		
		return $nav;
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: context_nav()
		
		Builds the main navigation menu for each context.
		
		Parameters:
			$context	- The context to build the nav for.
			
		Returns:
			The HTML necessary to display the menu.
	*/
	public function context_nav($context=null) 
	{	
		// Get a list of modules with a controller matching
		// $context ('content', 'appearance', 'settings', 'statistics', or 'developer')
		$module_list = module_list();
		foreach ($module_list as $module)
		{
			if (module_controller_exists($context, $module))
			{
				self::$actions[] = $module;
			}
		}

		unset($module_list);
	
		// Do we have any actions? 
		if (!count(self::$actions))
		{
			return '<ul class="dropdown-menu"></ul>';
		}
		
		// Grab our module permissions so we know who can see what on the sidebar
		$permissions = self::$ci->config->item('module_permissions');
		
		// Build up our menu array
		foreach (self::$actions as $module)
		{	
			// Make sure the user has permission to view this page.
			if ((isset($permissions[$context][$module]) && has_permission($permissions[$context][$module])) || (isset($permissions[$context]) && is_array($permissions[$context]) && !array_key_exists($module, $permissions[$context])))
			{
				// Grab our module config array, if any.
				$mod_config = module_config($module);
				
				$display_name = isset($mod_config['name']) ? $mod_config['name'] : $module;
				$title = isset($mod_config['description']) ? $mod_config['description'] : $module;
				
				$menu_topic = isset($mod_config['menu_topic'][$context]) ? $mod_config['menu_topic'][$context] : $display_name;
				
				// Drop-down menus?
				if (isset($mod_config['menus']) && isset($mod_config['menus'][$context]))
				{ 
					$menu_view = $mod_config['menus'][$context];
				} else
				{
					$menu_view = '';
				}
				
				self::$menu[$menu_topic][$module] = array(
						'title'			=> $title,
						'display_name'	=> $display_name,
						'menu_view'		=> $menu_view,
						'menu_topic'	=> $menu_topic
				);
			}
		}

		return self::build_sub_menu($context);
	}
	
	//--------------------------------------------------------------------

	/*
		Method: build_menu()
		
		Handles building out the HTML for the menu.
		
		Parameters:
			$context	- The context to build the menu for.	
	*/
	protected static function build_sub_menu($context) 
	{
		// Build a ul to return
		$list = "<ul class='dropdown-menu'>\n";
		
		//echo '<pre>'; die(print_r($this->menu));
		
		foreach (self::$menu as $topic_name => $topic)
		{		
			// If the topic has other items, we're not closed.
			$closed = true;
			
			// If there is more than one item in the topic, we need to build
			// out a menu based on the multiple items.
			if (count($topic) > 1)
			{
				$class = '';
			
				$list .= '<li><span{class}>'. ucwords($topic_name) .'</span>';
				$list .= '<ul>';
				
				foreach ($topic as $module => $vals)
				{ 	
					$class = $module == self::$ci->uri->segment(3) ? ' class="current"' : '';
				
					// If it has a sub-menu, echo out that menu only…
					if (isset($vals['menu_view']) && !empty($vals['menu_view']))
					{ 
						$view = self::$ci->load->view($vals['menu_view'], null, true);
						
						// To maintain backwards compatility, strip out and <ul> tags
						$view = str_ireplace('<ul>', '', $view);
						$view = str_ireplace('</ul>', '', $view);
						
						$list .= $view;
						
						$list = str_replace('{class}', $class, $list);
					}
					// Otherwise, it's a single item, so add it like normal
					else
					{
						$list .= self::build_item($module, $vals['title'], $vals['display_name'], $context, $vals['menu_view']);
					}
				}
					
				$list .= '</ul></li>';
			}
			else
			{
				foreach ($topic as $module => $vals)
				{ 
					$list .= self::build_item($module, $vals['title'], $vals['display_name'], $context, $vals['menu_view']);
				}
			}
			
		}
		
		$list .= "</ul>\n";
		
		return $list;
	}
	
	//--------------------------------------------------------------------
	
	
	/*
		Method: build_item()
		
		Handles building an individual list item (with sub-menus) for the menu.
		
		Parameters:
			$module			- The name of the module this link belongs to
			$title			- The title used on the link
			$display_name	- The name to display in the menu
			$context		- The name of the context
			$menu_view		- The name of the view file that contains the sub-menu
			
		Returns:
			The HTML necessary for a single item and it's sub-menus.
	*/
	protected static function build_item($module, $title, $display_name, $context, $menu_view='') 
	{
		// Is this the current module? 	
		$class = $module == self::$ci->uri->segment(3) ? 'class="current"' : '';
		
		$item  = '<li><a href="'. site_url(SITE_AREA .'/'. $context .'/'. $module) .'" '. $class;
		$item .= ' title="'. $title .'">'. ucwords(str_replace('_', '', $display_name)) ."</a>\n";
		
		// Sub Menus?
		if (!empty($menu_view))
		{
			// Only works if it's a valid view…
			$view = self::$ci->load->view($menu_view, null, true);
			
			$item .= $view;
		}
		
		$item .= "</li>\n";
				
		return $item;
	}
	
	//--------------------------------------------------------------------
}