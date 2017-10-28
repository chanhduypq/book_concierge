<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * settings controller
 */
class localization extends Front_Controller
{

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
	}

	//--------------------------------------------------------------------
	
	public function index()
	{
		redirect('/');
	}
	
	public function switcher($country='', $currency='')
	{
            $this->load->library('user_agent');
            
            $preUrl = $this->agent->referrer();
            $preUrl = explode('//', $preUrl);
            $preUrl = $preUrl[1];
            $preUrl = explode("/", $preUrl);

            if (count($preUrl) == 1 || $preUrl[1] == '') {
                $this->load->library('session');
                $this->session->set_userdata('country_iso', $this->input->post('country'));
                redirect('/');
                return;
            }


            if ($this->input->post() && $this->agent->is_referral())
		{			
			$this->load->model('country_model');
			$this->load->model('currency_model');
                        
			$country = $this->input->post('country');
                        $details = $this->country_model->find_by('iso', $country);
                        
                        if (!empty($details->domain)) {
                            redirect($details->domain);
                            exit;
                        }                        
                        
			$currency = $this->input->post('currency');
			
			$user_country = $this->session->userdata('country');
			$user_currency = $this->session->userdata('currency');
			
			//country switched
			if ($country != $user_country && $this->country_model->find($country)) {
				$this->session->set_userdata('country', $country);
								
				$def_currency = $this->currency_model->find_by('default_country', $country);
				if (!empty($def_currency))
					$this->session->set_userdata('currency', $def_currency->iso);
			} elseif($currency != $user_currency && $this->currency_model->find($currency)) {
				$this->session->set_userdata('currency', $currency);
			}
			
			redirect($this->agent->referrer());
		} else
			redirect('/');
	}	
	
	public function getStates($country='')
	{
		if(!$this->input->is_ajax_request()) {
			redirect('/');
		}
		
		$country = $this->input->get('country') ? $this->input->get('country') : $country;
		if (empty($country)) {
			echo json_encode(array());
			exit;
		}
		
		$this->load->model('region_model');
		$states = $this->region_model->find_all_by('country_iso', $country);
		if (empty($states)) {
			echo json_encode(array());
			exit;
		}
		
		$return = array();
		foreach ($states as $state) {
			$return[$country][$state->id] = $state->name;
		}
		
		echo json_encode($return);
		exit;
	}

}