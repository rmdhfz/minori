<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_DB_oci8_utility extends CI_DB_utility {
	protected $_list_databases	= 'SELECT username FROM dba_users'; // Schemas are actual usernames
	protected function _backup($params = array()){
		return $this->db->display_error('db_unsupported_feature');
	}

}
