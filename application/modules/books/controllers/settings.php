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

		$this->auth->restrict('Books.Settings.View');
		$this->lang->load('books');
		
		$this->load->helper('books');
		
		Template::set_block('sub_nav', 'settings/_sub_nav');

		Assets::add_module_js('books', 'books.js');
	}

	//--------------------------------------------------------------------


	/**
	 * Displays a list of form data.
	 *
	 * @return void
	 */
	public function index()
	{
		$this->featured_books();
	}
	
	public function featured($country='')
	{		
		$this->load->helper('array');
		
		$this->load->model('localization/country_model', null, true);
		$countries = $this->country_model->find_all();	
		
		if (empty($country) || !search_std_object($countries, $country))
			$country = $countries[0]->iso;
			
		$this->load->model('featuredbooks_model', null, true);
			
		if (isset($_POST['delete']))
		{
			$checked = $this->input->post('checked');

			if (is_array($checked) && count($checked))
			{
				$result = FALSE;
				foreach ($checked as $pid)
				{
					$result = $this->featuredbooks_model->where('ean', $pid)->delete_where("country_iso = '".$country."'");
				}

				if ($result)
				{
					Template::set_message(count($checked) .' '. lang('books_delete_success'), 'success');
				}
				else
				{
					Template::set_message(lang('books_delete_failure') . $this->featuredbooks_model->error, 'error');
				}
			}
		}
			
		$records = $this->featuredbooks_model->where('country_iso', $country)->find_all();
		Template::set('records', $records);
		
		Template::set('countries', $countries);		
		Template::set('current_country', $country);
				
		Template::set('toolbar_title', 'Manage Featured');
		Template::set_view('settings/featured');
		Template::render();
	}
	
	public function add_featured($country)
	{
		$this->load->helper('array');
		
		$this->load->model('localization/country_model', null, true);
		$countries = $this->country_model->find_all();	
		
		if (empty($country) || !search_std_object($countries, $country))
			redirect(SITE_AREA.'/settings/books/featured');
			
		$this->load->model('featuredbooks_model', null, true);
			
		if (isset($_POST['save']))
		{
			$ean = $this->input->post('selected_isbn');
			if (!empty($ean)) {
				$this->featuredbooks_model->insert(array('ean'=>$ean, 'country_iso'=>$country));
				Template::set_message('Featured Book Added', 'success');
				redirect(SITE_AREA.'/settings/books/featured/'.$country);
			}
		}
			
		Template::set('current_country', $country);
			
		Template::set('toolbar_title', 'Add Featured');
		Template::set_view('settings/add_featured');
		Template::render();
	}

	//--------------------------------------------------------------------



}