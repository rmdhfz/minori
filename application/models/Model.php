<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	function data_karyawan()
	{
		$this->load->database();
		$data = $this->db->query("SELECT * FROM karyawan");
		if ($data->num_rows() == 0) {
			echo json_encode(null);
			return;
		}
		$result = ['data' => []];
		foreach ($data->result() as $key => $value) {
			$result['data'][$key] = [
				$value->nip,
				$value->nama,
				$value->jabatan,
				$value->created_at
			];
		}
		echo json_encode($result);
	}
}
