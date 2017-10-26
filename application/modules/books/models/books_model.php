<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Books_model extends BF_Model {

    protected $table_name = "books";
    protected $key = "ean";
    protected $soft_deletes = false;
    protected $date_format = "datetime";
    protected $log_user = FALSE;
    protected $set_created = false;
    protected $set_modified = false;

    /*
      Customize the operations of the model without recreating the insert, update,
      etc methods by adding the method names to act as callbacks here.
     */
    protected $before_insert = array();
    protected $after_insert = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_find = array();
    protected $after_find = array();
    protected $before_delete = array();
    protected $after_delete = array();

    /*
      For performance reasons, you may require your model to NOT return the
      id of the last inserted row as it is a bit of a slow method. This is
      primarily helpful when running big loops over data.
     */
    protected $return_insert_id = TRUE;
    // The default type of element data is returned as.
    protected $return_type = "object";
    // Items that are always removed from data arrays prior to
    // any inserts or updates.
    protected $protected_attributes = array();

    /*
      You may need to move certain rules (like required) into the
      $insert_validation_rules array and out of the standard validation array.
      That way it is only required during inserts, not updates which may only
      be updating a portion of the data.
     */
    protected $validation_rules = array();
    protected $insert_validation_rules = array();
    protected $skip_validation = FALSE;

    //--------------------------------------------------------------------

    public function find($id = '') {
        $this->trigger('before_find');

        $query = $this->db->where(array($this->table_name . '.' . $this->key => $id))->or_where('isbn', $id)->get($this->table_name);

        if (!$query->num_rows()) {
            return FALSE;
        }

        $return = $query->{$this->_return_type()}();

        $return = $this->trigger('after_find', $return);

        if ($this->temp_return_type == 'json') {
            $return = json_encode($return);
        }

        // Reset our return type
        $this->temp_return_type = $this->return_type;

        return $return;
    }

    public function update($where = NULL, $data = NULL) {
        if (empty($where) || (isset($data['ean']) && empty($data['ean'])))
            return false;

        return parent::update($where, $data);
    }

    public function insert($data = null) {
        if ($this->skip_validation === false) {
            $data = $this->validate($data, 'insert');
            if ($data === false) {
                return false;
            }
        }

        $data = $this->trigger('before_insert', $data);

        if ($this->set_created === true && $this->log_user === true && !array_key_exists($this->created_by_field, $data)
        ) {
            $data[$this->created_by_field] = $this->auth->user_id();
        }

        // Insert it
        $sql = $this->db->insert_string($this->table_name, $data) . ' ON DUPLICATE KEY UPDATE ean=' . $data['ean'];
        $this->db->query($sql);
        $status = $this->db->affected_rows();

        if ($status == false) {
            $this->error = $this->get_db_error_message();
        } elseif ($this->return_insert_id) {
            $id = $data['ean'];

            $status = $this->trigger('after_insert', $id);
        }
        
        // disabled sphinx RT Index for now
        return $status;

        // insert to real time index
        $rt_conn = mysql_connect('45.33.36.14:9306', '', '');

        $record = array(
            'id' => $data['ean'],
            'isbn' => $data['ean'],
            'isbn10' => $data['isbn'],
            'isbn_text' => $data['isbn'],
            'asin' => isset($data['ASIN']) ? $data['ASIN'] : '',
            'name' => mysql_real_escape_string($data['name']),
            'author' => mysql_real_escape_string($data['author']),
            'description' => isset($data['description']) ? mysql_real_escape_string($data['description']) : '',
            'sales_rank' => isset($data['sales_rank']) ? (int) $data['sales_rank'] : 0,
            'cdn_image' => isset($data['cdn_image']) ? $data['cdn_image'] : '',
            'manufacturer' => isset($data['manufacturer']) ? $data['manufacturer'] : '',
            'book_format' => isset($data['book_format']) ? $data['book_format'] : '',
            'publisher' => isset($data['publisher']) ? $data['publisher'] : '',
            'edition' => isset($data['edition']) ? $data['edition'] : '',
            'binding' => isset($data['binding']) ? $data['binding'] : '',
            'views' => 0,
            'timestamp' => strtotime('NOW'),
            'length' => 0,
            'name_2' => mysql_real_escape_string($data['name']),
            'author_2' => mysql_real_escape_string($data['author']),
        );

        $sql = 'INSERT INTO books_rt(' . implode(',', array_keys($record)) . ') ' . "VALUES('" . implode("','", $record) . "')";
        mysql_query($sql, $rt_conn);
        // end

        return $status;
    }

}
