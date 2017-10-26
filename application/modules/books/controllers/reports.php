<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * reports controller
 */
class reports extends Admin_Controller
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

		$this->auth->restrict('Books.Reports.View');
		$this->lang->load('books');
		
		Template::set_block('sub_nav', 'reports/_sub_nav');

		Assets::add_module_js('books', 'books.js');
	}

	//--------------------------------------------------------------------



}