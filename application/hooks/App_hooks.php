<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2013, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Application Hooks
 *
 * This class provides a set of methods used for the CodeIgniter hooks.
 * http://www.codeigniter.com/user_guide/general/hooks.html
 *
 * @package    Bonfire
 * @subpackage Hooks
 * @category   Hooks
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/core/hooks.html
 *
 */
class App_hooks
{


	/**
	 * Stores the CodeIgniter core object.
	 *
	 * @access private
	 *
	 * @var object
	 */
	private $ci;

	/**
	 * List of pages where the hooks are not run.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $ignore_pages = array(LOGIN_URL, '/users/logout', REGISTER_URL, '/users/forgot_password', '/users/activate', '/users/resend_activation', '/images');

	//--------------------------------------------------------------------


	/**
	 * Costructor
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
	}//end __construct()

	//--------------------------------------------------------------------


	/**
	 * Stores the name of the current uri in the session as 'previous_page'.
	 * This allows redirects to take us back to the previous page without
	 * relying on inconsistent browser support or spoofing.
	 *
	 * Called by the "post_controller" hook.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function prep_redirect()
	{
		if (!class_exists('CI_Session'))
		{
			$this->ci->load->library('session');
		}

		if (!in_array($this->ci->uri->ruri_string(), $this->ignore_pages))
		{
			$this->ci->session->set_userdata('previous_page', current_url());
		}
	}//end prep_redirect()

	//--------------------------------------------------------------------

	/**
	 * Store the requested page in the session data so we can use it
	 * after the user logs in.
	 *
	 * Called by the "pre_controller" hook.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function save_requested()
	{
		if (!class_exists('CI_Session'))
		{
			$this->ci->load->library('session');
		}

		if (!in_array($this->ci->uri->ruri_string(), $this->ignore_pages))
		{
			$this->ci->session->set_userdata('requested_page', current_url());
		}
	}//end save_requested()

	//--------------------------------------------------------------------


	/**
	 * Check the online/offline status of the site.
	 *
	 * Called by the "post_controller_constructor" hook.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 */
	public function check_site_status()
	{
		if (!class_exists('Settings_lib'))
		{
			$this->ci->load->library('settings/settings_lib');
		}
		
		if ($this->ci->settings_lib->item('site.status') == 0)
		{
			if (!class_exists('Auth'))
			{
				$this->ci->load->library('users/auth');
			}

			if (!$this->ci->auth->has_permission('Site.Signin.Offline'))
			{
				include (APPPATH .'errors/offline'. EXT);
				die();
			}
		}
	}//end check_site_status()
	
	
	public function localize()
	{
		if (!class_exists('CI_Session'))
		{
			$this->ci->load->library('session');
		}
		
		if (false && $this->ci->session->userdata('country')) {
			return;
		}
                
                switch($_SERVER[HTTP_HOST]) {
                    case 'bookconcierge.my':
                    case 'bookconcierge.com.my':
                        $this->ci->session->set_userdata('country', 'MY');
                        $this->ci->session->set_userdata('currency', 'MYR');
                        break;
                    case 'bookconcierge.hk':
                        $this->ci->session->set_userdata('country', 'HK');
                        $this->ci->session->set_userdata('currency', 'HKD');
                        break;
                    case 'bookconcierge.sg':
                    case 'bookconcierge.com.sg':
                        $this->ci->session->set_userdata('country', 'SG');
                        $this->ci->session->set_userdata('currency', 'SGD');
                        break;
                    case 'bookconcierge.co':
                        switch(true) {
                            case strstr($_SERVER[REQUEST_URI], 'ind/'):
                                $this->ci->session->set_userdata('country', 'ID');
                                $this->ci->session->set_userdata('currency', 'IDR');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'id/'):
                                $this->ci->session->set_userdata('country', 'IN');
                                $this->ci->session->set_userdata('currency', 'INR');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'th/'):
                                $this->ci->session->set_userdata('country', 'TH');
                                $this->ci->session->set_userdata('currency', 'THB');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'bangladesh/'):
                                $this->ci->session->set_userdata('country', 'BD');
                                $this->ci->session->set_userdata('currency', 'BDT');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'cambodia/'):
                                $this->ci->session->set_userdata('country', 'KH');
                                $this->ci->session->set_userdata('currency', 'KHR');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'china/'):
                                $this->ci->session->set_userdata('country', 'CN');
                                $this->ci->session->set_userdata('currency', 'CNY');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'jp/'):
                                $this->ci->session->set_userdata('country', 'JP');
                                $this->ci->session->set_userdata('currency', 'JPY');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'myanmar/'):
                                $this->ci->session->set_userdata('country', 'MM');
                                $this->ci->session->set_userdata('currency', 'MMK');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'nepal/'):
                                $this->ci->session->set_userdata('country', 'NP');
                                $this->ci->session->set_userdata('currency', 'NPR');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'ph/'):
                                $this->ci->session->set_userdata('country', 'PH');
                                $this->ci->session->set_userdata('currency', 'PHP');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'korea/'):
                                $this->ci->session->set_userdata('country', 'KR');
                                $this->ci->session->set_userdata('currency', 'KRW');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'srilanka/'):
                                $this->ci->session->set_userdata('country', 'LK');
                                $this->ci->session->set_userdata('currency', 'LKR');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'taiwan/'):
                                $this->ci->session->set_userdata('country', 'TW');
                                $this->ci->session->set_userdata('currency', 'TWD');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'vietnam/'):
                                $this->ci->session->set_userdata('country', 'VN');
                                $this->ci->session->set_userdata('currency', 'VND');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'can/'):
                                $this->ci->session->set_userdata('country', 'CA');
                                $this->ci->session->set_userdata('currency', 'CAD');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'usa/'):
                                $this->ci->session->set_userdata('country', 'US');
                                $this->ci->session->set_userdata('currency', 'USD');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'aus/'):
                                $this->ci->session->set_userdata('country', 'AU');
                                $this->ci->session->set_userdata('currency', 'AUD');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'russia/'):
                                $this->ci->session->set_userdata('country', 'RU');
                                $this->ci->session->set_userdata('currency', 'RUB');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'uae/'):
                                $this->ci->session->set_userdata('country', 'AE');
                                $this->ci->session->set_userdata('currency', 'AED');
                                break;
                            case strstr($_SERVER[REQUEST_URI], 'uk/'):
                                $this->ci->session->set_userdata('country', 'UK');
                                $this->ci->session->set_userdata('currency', 'GBP');
                                break;
                        }
                        break;
                    default:
                        $this->ci->session->set_userdata('country', 'HK');
                        $this->ci->session->set_userdata('currency', 'HKD');
                        break;
                }
		
		return;
		
		$vistor_data = array();
		
		if ($this->ci->auth->is_logged_in()) {
			$current_user = $this->ci->auth->user();	
			
			$this->ci->load->model('users/user_model');
					
			$user = $this->ci->user_model->find_user_and_meta($current_user->id);
			if ($user && isset($user->country)) {
				$vistor_data['countryCode'] = $user->country;
			}		
		}
		
		if (!isset($vistor_data['countryCode']) && empty($vistor_data['countryCode']))
			$vistor_data = visitor_data();
		
		$this->ci->load->model('localization/country_model');
		if ($country = $this->ci->country_model->find($vistor_data['countryCode'])) {
			$this->ci->session->set_userdata('country', $country->iso);
			
			$this->ci->load->model('localization/currency_model');
			if ($currency = $this->ci->currency_model->find_by('default_country', $country->iso))
				$this->ci->session->set_userdata('currency', $currency->iso);
			else
				$this->ci->session->set_userdata('currency', 'HKD');
		} else {
			$this->ci->session->set_userdata('country', 'HK');
			$this->ci->session->set_userdata('currency', 'HKD');
		}
		
	}
	
	public function process_image_fetch()
	{
		/*
		$this->ci->load->model('books/image_queue_model');
		$image_queue = $this->ci->image_queue_model->limit(10)->find_all();
		*/
		
		if ($_SERVER['HTTP_HOST'] == 'localhost')
			return;
		
		if (!function_exists('add_image_queue')) {
			$this->ci->load->helper('books/books');
		}
		
		$image_queue = add_image_queue(false, true);		
		
		if (empty($image_queue))
			return;
				
		ob_end_flush(); flush(); session_write_close();	
		
		$isbns = array();
		foreach ($image_queue as $isbn) {
			$isbns[] = $isbn;
		}
		
		ob_start();
		$this->ci->load->library('amazon');
		$data = $this->ci->amazon->fetch(implode(',', $isbns));		
				
		$this->ci->load->model('books/books_model');
		//$this->ci->load->model('books/books_meta_model');
		foreach ($data as $item) {
			//$this->ci->image_queue_model->delete($item['ean']);
			$this->ci->books_model->update($item['ean'], $item);
			
			/**********
			// add additional amazon data
			$insert_query = $this->ci->db->insert_string('books_meta', array('ean'=>$item['ean'], 'meta_option'=>'ASIN', 'meta_value'=>$item['ASIN'], 'engine'=>'amazon.com'));
			$sql = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query).";";
			$this->ci->db->query($sql);
			
			$insert_query = $this->ci->db->insert_string('books_meta', array('ean'=>$item['ean'], 'meta_option'=>'link', 'meta_value'=>$item['link'], 'engine'=>'amazon.com'));
			$sql = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query).";";
			$this->ci->db->query($sql);
			***********/
		}
		ob_end_clean();
	}

	//--------------------------------------------------------------------

}//end class