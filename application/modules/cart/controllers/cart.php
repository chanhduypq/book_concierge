<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cart controller
 */
class cart extends Front_Controller
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

		$this->load->library('form_validation');
		$this->lang->load('cart');
				
		$this->load->helper('books/books');
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

		Template::render();
	}
	
	public function add($ean='', $store_id='', $condition='new') {
		$error = false;
		
		if (empty($ean) || empty($store_id)) {
			$error = true;
		}
		
		if (!$error) {
			$this->load->model('books/books_model');
			$details = $this->books_model->find($ean);
			if (empty($details)) {
				$error = true;
			}
		}
		
		// TO DO: When more user try saving the complete objects into cart
		// to reduce the queries to db
		
		if ($error) {
			if ($this->input->is_ajax_request()) {
				exit;
			} else {
				Template::set_message('Unable to add this product to your cart. Please try again.', 'error');
				redirect('/');
			}
		} else {
			$this->load->model('books/books_prices_model');
			$price = $this->books_prices_model->find_by(array('ean'=>$ean, 'engine'=>$store_id, 'condition'=>$condition));					
						
			$data = array(
               'id'      => $ean,
               'qty'     => 1,
               'price'   => $price->price,
               'name'    => $details->name,
			   'image'	 => book_image_url($details->ean, $details->cdn_image),
			   'store_id'=>	$store_id,
			   'condition' => $condition
            );		

			$this->cart->insert($data);
		}
		
		if ($this->input->is_ajax_request()) {
			echo 'SUCCESS';
			exit;
		} else {
			redirect('/cart');
		}
	}
	
	public function delete($row_id='') {
		if (!empty($row_id) && $this->cart->total_items() > 0) {
			$data = array(
				'rowid' => $row_id,
				'qty' 	=> 0
			);
			
			$this->cart->update($data);
		}
		
		if ($this->input->is_ajax_request()) {
			echo 'SUCCESS';
			exit;
		} else {
			$this->load->library('user_agent');
			if ($this->agent->is_referral()) {
				redirect($this->agent->referrer());
			} else {
				redirect('/cart');
			}
		}
	}
	
	public function update_cart_view()
	{
		if (!$this->input->is_ajax_request()) {
			redirect('/cart');
		}
		
		displayCart('summary');
		exit;
	}

	//--------------------------------------------------------------------



}