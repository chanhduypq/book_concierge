<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Cart extends CI_Cart {
	var $user = false;

    function __construct($params = array()) 
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();
		
		// Set user if already logged in
		if ($this->CI->auth->is_logged_in() === TRUE) {
			$this->user = clone $this->CI->auth->user();
		}

		// Are any config settings being passed manually?  If so, set them
		$config = array();
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				$config[$key] = $val;
			}
		}

		// Load the Sessions class
		$this->CI->load->library('session', $config);
		
		// check if user is logged in and have a saved cart
		if ($this->user) {
			$this->CI->load->model('users/user_model');
			$meta = $this->CI->user_model->find_meta_for($this->user->id, array('cart'));
			
			if ($meta && !empty($meta->cart)) {
				$cart = unserialize($meta->cart);
				if (is_array($cart)) {
					$this->CI->session->set_userdata('cart_contents', $cart);
					$this->_cart_contents = $this->CI->session->userdata('cart_contents');
					
					$cart_loaded = true;
				}
			}
		}

		// Grab the shopping cart array from the session table, if it exists
		if ($this->CI->session->userdata('cart_contents') !== FALSE)
		{
			$this->_cart_contents = $this->CI->session->userdata('cart_contents');
		}
		else
		{
			// No cart exists so we'll set some base values
			$this->_cart_contents['cart_total'] = 0;
			$this->_cart_contents['total_items'] = 0;
		}

		log_message('debug', "Cart Class Initialized");
		
		$this->product_name_rules = '\d\D';
	}
	
	/*
	 * Returns data for products in cart
	 * 
	 * @param integer $product_id used to fetch only the quantity of a specific product
	 * @return array|integer $in_cart an array in the form (id => quantity, ....) OR quantity if $product_id is set
	 */
	public function in_cart($product_id = null) {
		if ($this->total_items() > 0)
		{
			$in_cart = array();
			// Fetch data for all products in cart
			foreach ($this->contents() AS $item)
			{
				$in_cart[$item['id']] = $item['qty'];
			}
			if ($product_id)
			{
				if (array_key_exists($product_id, $in_cart))
				{
					return $in_cart[$product_id];
				}
				return null;
			}
			else
			{
				return $in_cart;
			}
		}
		return null;    
	}
	
	public function all_item_count()
	{
		$total = 0;
	
		if ($this->total_items() > 0)
		{
			foreach ($this->contents() AS $item)
			{
				$total = $item['qty'] + $total;
			}
		}
	
		return $total;
	}
	
	function _save_cart()
	{
		parent::_save_cart();
		
		// now save to database if user is logged in
		if ($this->user) {
			$cart_content = serialize($this->CI->session->userdata('cart_contents'));
						
			$this->CI->load->model('users/user_model');			
			$this->CI->user_model->save_meta_for($this->user->id, array('cart'=>$cart_content));
		}
	}
 }