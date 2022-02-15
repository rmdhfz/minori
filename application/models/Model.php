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
		json($result);
	}
	function simpan_karyawan()
	{
		$nip		= post('nip');
		$nama		= post('nama');
		$jabatan	= post('jabatan');

		$this->load->database();
		$check = $this->db->query("SELECT nip FROM karyawan WHERE nip = ?", [$nip])->num_rows();
		if ($check > 0) {
			json(response(false, 400, 'nip sudah terdaftar'));
		}

		$simpan = $this->db->insert('karyawan', [
			'nip'		=> $nip,
			'nama'		=> $nama,
			'jabatan'	=> $jabatan
		]);

		if (!$simpan) {
			json(response(false, 500, 'gagal simpan data karyawan'));
		}
		json(response(true, 200, 'berhasil simpan data karyawan'));
	}
}
