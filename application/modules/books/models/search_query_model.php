<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class search_query_model extends BF_Model {

	protected $table_name	= "search_query";
	protected $key			= "query";
	protected $soft_deletes	= false;
	protected $date_format	= "datetime";

	protected $log_user 	= FALSE;

	protected $set_created	= true;
	protected $set_modified = false;


	protected $return_insert_id 	= false;

	// The default type of element data is returned as.
	protected $return_type 			= "object";

	// Items that are always removed from data arrays prior to
	// any inserts or updates.
	protected $protected_attributes = array();

	protected $validation_rules 		= array();
	protected $insert_validation_rules 	= array();
	protected $skip_validation 			= FALSE;

	//--------------------------------------------------------------------
}
