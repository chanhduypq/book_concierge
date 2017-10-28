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

		$this->auth->restrict('Shippingrates.Settings.View');
		$this->lang->load('shippingrates');
		
		$this->load->model('shippingrates_model', null, true);
		$this->load->model('country_shippingrates_model', null, true);
		$this->load->model('bookstores/bookstores_model', null, true);
		$this->load->model('bookstores/stores_model', null, true);
		
		$this->load->model('localization/country_model', null, true);
		$this->load->model('localization/region_model', null, true);
		
		Template::set_block('sub_nav', 'settings/_sub_nav');

		Assets::add_module_js('shippingrates', 'shippingrates.js');
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
	
		$countries = $this->country_model->find_all();	
		if (empty($country) || !search_std_object($countries, $country))
			$country = $countries[0]->iso;
			
		if (isset($_POST['rate']))
		{
			$data = $this->input->post('rate');
			$currency = $this->input->post('currency');
			$availability = $this->input->post('availability');

			if (is_array($data) && count($data))
			{
				$result = FALSE;				
				foreach ($data as $region=>$rates)
				{
					if (is_array($rates) && count($rates)) {
						foreach ($rates as $book_store=>$rate) {
							if (is_numeric($region)) {
								$this->shippingrates_model->where('region_id', $region)->delete_where("store_id = '".$book_store."'");
								
								if ($rate === '0' || (float)$rate)
									$this->shippingrates_model->insert(array('region_id'=>$region, 'store_id'=>$book_store, 'rate'=>$rate, 'currency'=>$currency[$region][$book_store], 'availability'=>$availability[$region][$book_store]));
							} else {
								$this->country_shippingrates_model->where('iso', $region)->delete_where("store_id = '".$book_store."'");
								
								if ($rate === '0' || (float)$rate)
									$this->country_shippingrates_model->insert(array('iso'=>$region, 'store_id'=>$book_store, 'rate'=>$rate, 'currency'=>$currency[$region][$book_store], 'availability'=>$availability[$region][$book_store]));
							}
						}
					}
					$result = true;
				}

				if ($result)
				{
					Template::set_message('Shipping Rates Updated', 'success');
				}
				else
				{
					Template::set_message('Error: ' . $this->bookstores_model->error, 'error');
				}
			}
		}
			
		$country_stores = $this->bookstores_model->where('country_iso', $country)->find_all();
		$country_regions = $this->region_model->where('country_iso', $country)->find_all();
		
		$rate_data = $this->shippingrates_model->where('country_iso', $country)->find_all();
		$rates = array();
		if ($rate_data) {
			foreach ($rate_data as $data) {
				$rates[$data->region_id][$data->store_id] = array('rate'=>$data->rate, 'currency'=>$data->currency, 'availability'=>$data->availability);
			}
		}
		
		$rate_data = $this->country_shippingrates_model->where('iso', $country)->find_all();
		if ($rate_data) {
			foreach ($rate_data as $data) {
				$rates[$data->iso][$data->store_id] = array('rate'=>$data->rate, 'currency'=>$data->currency, 'availability'=>$data->availability);
			}
		}
		
		$this->load->model('localization/currency_model');
		$currencies = $this->currency_model->find_all();
			
		Template::set('records', $country_regions);
		Template::set('countries', $countries);
		Template::set('country_stores', $country_stores);		
		
		Template::set('rates', $rates);	
		
		Template::set('current_country', $country);
		Template::set('currencies', $currencies);
		
		Template::set('toolbar_title', 'Manage shippingrates');
		Template::set_view('settings/index');
		Template::render();
	}
	
	public function country($country) {
		$this->index($country);
	}

	//--------------------------------------------------------------------



}