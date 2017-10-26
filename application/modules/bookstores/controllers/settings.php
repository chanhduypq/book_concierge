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

		$this->auth->restrict('Bookstores.Settings.View');
		$this->load->model('bookstores_model', null, true);
		$this->load->model('stores_model', null, true);
		$this->lang->load('bookstores');
		
		$this->load->model('localization/country_model', null, true);
		
		Template::set_block('sub_nav', 'settings/_sub_nav');

		Assets::add_module_js('bookstores', 'bookstores.js');
	}

	//--------------------------------------------------------------------


	/**
	 * Displays a list of form data.
	 *
	 * @return void
	 */
	public function index($country='')
	{		
		$this->load->helper('array');
		
		$stores = $this->stores_model->find_all();
		$countries = $this->country_model->find_all();	
		if (empty($country) || !search_std_object($countries, $country))
			$country = $countries[0]->iso;
			
		if (isset($_POST['save']))
		{
			$checked = $this->input->post('checked');

			if (is_array($checked) && count($checked))
			{
				$result = FALSE;
				$this->bookstores_model->delete_where("country_iso = '".$country."'");
				foreach ($checked as $pid)
				{
					$this->bookstores_model->insert(array('store_id'=>$pid, 'country_iso'=>$country));
					$result = true;
				}

				if ($result)
				{
					Template::set_message(count($checked) .' Bookstores set for this country', 'success');
				}
				else
				{
					Template::set_message('Error: ' . $this->bookstores_model->error, 'error');
				}
			}
		}
			
		$country_stores = $this->bookstores_model->where('country_iso', $country)->find_all();
			
		Template::set('records', $stores);
		Template::set('countries', $countries);
		
		Template::set('current_country', $country);
		Template::set('country_stores', $country_stores);
		
		Template::set('toolbar_title', 'Manage Bookstores');
		Template::set_view('settings/index');
		Template::render();
	}
	
	public function country($country) {
		$this->index($country);
	}

	//--------------------------------------------------------------------

}