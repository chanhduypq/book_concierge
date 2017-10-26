<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * settings controller
 */
class settings extends Admin_Controller
{

	//--------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict('Cart.Settings.View');
		$this->lang->load('cart');
		
		Template::set_block('sub_nav', 'settings/_sub_nav');

		Assets::add_module_js('cart', 'cart.js');
	}

	//--------------------------------------------------------------------


	/**
	 * Displays a list of form data.
	 *
	 * @return void
	 */
	public function index()
	{

		Template::set('toolbar_title', 'Manage cart');
		Template::render();
	}

	//--------------------------------------------------------------------



}