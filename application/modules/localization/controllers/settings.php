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

		$this->auth->restrict('Localization.Settings.View');
		$this->load->model('country_model', null, true);
		
		$this->lang->load('localization');
		$this->load->config('config');
		
		Template::set_block('sub_nav', 'settings/_sub_nav');

		Assets::add_module_js('localization', 'localization.js');
	}

	//--------------------------------------------------------------------


	/**
	 * Displays a list of form data.
	 *
	 * @return void
	 */
	public function index()
	{
		// Deleting anything?
		if (isset($_POST['delete']))
		{
			$checked = $this->input->post('checked');

			if (is_array($checked) && count($checked))
			{
				$result = FALSE;
				foreach ($checked as $pid)
				{
					$result = $this->country_model->delete($pid);
				}

				if ($result)
				{
					Template::set_message(count($checked) .' '. lang('localization_delete_success'), 'success');
					@unlink($this->config->item('localization_countries_cache'));
				}
				else
				{
					Template::set_message(lang('localization_delete_failure') . $this->country_model->error, 'error');
				}
			}
		}

		$records = $this->country_model->find_all();

		Template::set('records', $records);
		Template::set('toolbar_title', 'Manage localization');
		Template::render();
	}
	
	public function currencies()
	{
	
		$this->load->model('currency_model', null, true);

		// Deleting anything?
		if (isset($_POST['delete']))
		{
			$checked = $this->input->post('checked');

			if (is_array($checked) && count($checked))
			{
				$result = FALSE;
				foreach ($checked as $pid)
				{
					$result = $this->currency_model->delete($pid);
				}

				if ($result)
				{
					Template::set_message(count($checked) .' '. lang('localization_delete_success'), 'success');
				}
				else
				{
					Template::set_message(lang('localization_delete_failure') . $this->currency_model->error, 'error');
				}
			}
		}

		$records = $this->currency_model->find_all();

		Template::set('records', $records);
	
		Template::set('toolbar_title', 'Manage Currencies');
		Template::render();
	}
	
	public function regions($country='')
	{
		$this->load->model('region_model', null, true);
		
		// Deleting anything?
		if (isset($_POST['delete']))
		{
			$checked = $this->input->post('checked');

			if (is_array($checked) && count($checked))
			{
				$result = FALSE;
				foreach ($checked as $pid)
				{
					$result = $this->region_model->delete($pid);
				}

				if ($result)
				{
					Template::set_message(count($checked) .' '. lang('localization_delete_success'), 'success');
				}
				else
				{
					Template::set_message(lang('localization_delete_failure') . $this->region_model->error, 'error');
				}
			}
		}
		
		$this->load->helper('array');
		
		$countries = $this->country_model->find_all();	
		if (empty($country) || !search_std_object($countries, $country))
			$country = $countries[0]->iso;
		$records = $this->region_model->where('country_iso', $country)->order_by('name')->find_all();

		Template::set('records', $records);
		
		Template::set('countries', $countries);
		Template::set('current_country', $country);
	
		Template::set('toolbar_title', 'Manage Currencies');
		Template::render();
	}

	//--------------------------------------------------------------------


	/**
	 * Creates a localization object.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->auth->restrict('Localization.Settings.Create');

		if (isset($_POST['save']))
		{
			if ($insert_id = $this->save_localization())
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_create_record') .': '. $insert_id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message(lang('localization_create_success'), 'success');
				redirect(SITE_AREA .'/settings/localization');
			}
			else
			{
				Template::set_message(lang('localization_create_failure') . $this->country_model->error, 'error');
				Template::set('localization', $_POST);
			}
		}
		Assets::add_module_js('localization', 'localization.js');

		Template::set('toolbar_title', lang('localization_create') . ' Country');
		Template::render();
	}
	
	public function create_currency()
	{
		$this->auth->restrict('Localization.Settings.Create');

		if (isset($_POST['save']))
		{
			if ($insert_id = $this->save_currency())
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_create_record') .': '. $insert_id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message('Currency Saved', 'success');
				redirect(SITE_AREA .'/settings/localization/currencies');
			}
			else
			{
				Template::set_message(lang('localization_create_failure') . $this->currency_model->error, 'error');
				Template::set('localization', $_POST);
			}
		}
		Assets::add_module_js('localization', 'localization.js');
		
		$countries = $this->country_model->find_all();
		Template::set('countries', $countries);
		Template::set('toolbar_title', lang('localization_create') . ' Country');
		
		Template::set_view('settings/currency_form.php');
		Template::render();
	}
	
	public function create_region($country='')
	{
		$this->auth->restrict('Localization.Settings.Create');
		
		$this->load->helper('array');
		
		$countries = $this->country_model->find_all();	
		if (empty($country) || !search_std_object($countries, $country))
			$country = $countries[0]->iso;
			
		$localization = array();
		$localization['country_iso'] = $country;

		if (isset($_POST['save']))
		{
			if ($insert_id = $this->save_region())
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_create_record') .': '. $insert_id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message('Region Saved', 'success');
				redirect(SITE_AREA .'/settings/localization/regions/'.$country);
			}
			else
			{
				Template::set_message(lang('localization_create_failure') . $this->currency_model->error, 'error');
				$localization = $_POST;
			}
		}
		
		Assets::add_module_js('localization', 'localization.js');
		
		Template::set('countries', $countries);
		Template::set('localization', $localization);
		
		Template::set('toolbar_title', lang('localization_create') . ' Region');
		
		Template::set_view('settings/region_form.php');
		Template::render();
	}

	//--------------------------------------------------------------------


	/**
	 * Allows editing of localization data.
	 *
	 * @return void
	 */
	public function edit()
	{
		$id = $this->uri->segment(5);

		if (empty($id))
		{
			Template::set_message(lang('localization_invalid_id'), 'error');
			redirect(SITE_AREA .'/settings/localization');
		}

		if (isset($_POST['save']))
		{
			$this->auth->restrict('Localization.Settings.Edit');

			if ($this->save_localization('update', $id))
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_edit_record') .': '. $id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message(lang('localization_edit_success'), 'success');
			}
			else
			{
				Template::set_message(lang('localization_edit_failure') . $this->country_model->error, 'error');
			}
		}
		else if (isset($_POST['delete']))
		{
			$this->auth->restrict('Localization.Settings.Delete');

			if ($this->country_model->delete($id))
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_delete_record') .': '. $id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message(lang('localization_delete_success'), 'success');

				redirect(SITE_AREA .'/settings/localization');
			}
			else
			{
				Template::set_message(lang('localization_delete_failure') . $this->country_model->error, 'error');
			}
		}
		Template::set('localization', $this->country_model->find($id));
		Template::set('toolbar_title', lang('localization_edit') .' localization');
		Template::render();
	}
	
	public function edit_currency()
	{
		$this->load->model('currency_model', null, true);
		
		$id = $this->uri->segment(5);

		if (empty($id))
		{
			Template::set_message(lang('localization_invalid_id'), 'error');
			redirect(SITE_AREA .'/settings/localization/currencies');
		}

		if (isset($_POST['save']))
		{
			$this->auth->restrict('Localization.Settings.Edit');

			if ($this->save_currency('update', $id))
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_edit_record') .': '. $id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message(lang('localization_edit_success'), 'success');
			}
			else
			{
				Template::set_message(lang('localization_edit_failure') . $this->country_model->error, 'error');
			}
		}
		Template::set('localization', $this->currency_model->find($id));
		
		$countries = $this->country_model->find_all();
		Template::set('countries', $countries);
		
		Template::set_view('settings/currency_form.php');
		
		Template::set('toolbar_title', lang('localization_edit') .' localization');
		Template::render();
	}
	
	public function edit_region()
	{
		$this->load->model('region_model', null, true);
		
		$id = $this->uri->segment(5);

		if (empty($id))
		{
			Template::set_message(lang('localization_invalid_id'), 'error');
			redirect(SITE_AREA .'/settings/localization/regions');
		}

		if (isset($_POST['save']))
		{
			$this->auth->restrict('Localization.Settings.Edit');

			if ($this->save_region('update', $id))
			{
				// Log the activity
				log_activity($this->current_user->id, lang('localization_act_edit_record') .': '. $id .' : '. $this->input->ip_address(), 'localization');

				Template::set_message(lang('localization_edit_success'), 'success');
			}
			else
			{
				Template::set_message(lang('localization_edit_failure') . $this->region_model->error, 'error');
			}
		}
		Template::set('localization', $this->region_model->find($id));
		
		$countries = $this->country_model->find_all();
		Template::set('countries', $countries);
		
		Template::set_view('settings/region_form.php');
		
		Template::set('toolbar_title', lang('localization_edit') .' localization');
		Template::render();
	}


	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/**
	 * Summary
	 *
	 * @param String $type Either "insert" or "update"
	 * @param Int	 $id	The ID of the record to update, ignored on inserts
	 *
	 * @return Mixed    An INT id for successful inserts, TRUE for successful updates, else FALSE
	 */
	private function save_localization($type='insert', $id=0)
	{
		if ($type == 'update')
		{
			$_POST['id'] = $id;
		}

		// make sure we only pass in the fields we want
		
		$data = array();
		$data['iso']        = $this->input->post('localization_country_code');
		$data['name']        = $this->input->post('localization_country_name');
                $data['domain']        = $this->input->post('domain');

		if ($type == 'insert')
		{
			$this->country_model->insert($data);
			return true;
		}
		elseif ($type == 'update')
		{
			$return = $this->country_model->update($id, $data);
		}
		
		@unlink($this->config->item('localization_countries_cache'));

		return $return;
	}
	
	private function save_currency($type='insert', $id=0)
	{
		$this->load->model('currency_model', null, true);
	
		if ($type == 'update')
		{
			$_POST['id'] = $id;
		}

		// make sure we only pass in the fields we want
		
		$data = array();
		$data['iso']        			= $this->input->post('iso');
		$data['default_country']        = $this->input->post('default_country');

		if ($type == 'insert')
		{
			$id = $this->currency_model->insert($data);

			if ($id)
			{
				$return = $id;
			}
			else
			{
				$return = FALSE;
			}
		}
		elseif ($type == 'update')
		{
			$return = $this->currency_model->update($id, $data);
		}

		return $return;
	}
	
	private function save_region($type='insert', $id=0)
	{
		$this->load->model('region_model', null, true);
	
		if ($type == 'update')
		{
			$_POST['id'] = $id;
		}

		// make sure we only pass in the fields we want
		
		$data = array();
		$data['name']        			= $this->input->post('name');
		$data['abbrev']        			= $this->input->post('abbrev');
		$data['country_iso'] 	       = $this->input->post('country_iso');

		if ($type == 'insert')
		{
			$id = $this->region_model->insert($data);

			if ($id)
			{
				$return = $id;
			}
			else
			{
				$return = FALSE;
			}
		}
		elseif ($type == 'update')
		{
			$return = $this->region_model->update($id, $data);
		}

		return $return;
	}

	//--------------------------------------------------------------------


}