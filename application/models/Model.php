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
		$no = 0;
		foreach ($data->result() as $key => $value) { $no++;
			$result['data'][$key] = [
				$no,
				$value->nip,
				$value->nama,
				$value->jabatan,
				change_format_date($value->created_at),
				"<a id='edit' data-id='$value->id'>edit</a> | <a id='delete' data-id='$value->id'>delete</a>"
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
	function id_karyawan()
	{
		$id = post('id');
		$this->load->database();
		$check = $this->db->query("SELECT id FROM karyawan WHERE id = ?", [$id])->num_rows();
		if ($check == 0) {
			json(response(false, 400, 'data karyawan tidak ditemukan'));
		}
		$data = $this->db->query("SELECT * FROM karyawan WHERE id = ?", [$id])->row();
		json(response(true, 200, 'berhasil ambil data karyawan', $data));
	}
	function edit_karyawan()
	{
		$id			= post('id');
		$nip		= post('nip');
		$nama		= post('nama');
		$jabatan	= post('jabatan');

		$this->load->database();

		$edit = $this->db->where('id', $id)->update('karyawan', [
			'nip'		=> $nip,
			'nama'		=> $nama,
			'jabatan'	=> $jabatan
		]);

		if (!$edit) {
			json(response(false, 500, 'gagal edit data karyawan'));
		}
		json(response(true, 200, 'berhasil edit data karyawan'));
	}
	function hapus_karyawan()
	{
		$id = post('id');
		$this->load->database();
		$check = $this->db->query("SELECT id FROM karyawan WHERE id = ?", [$id])->num_rows();
		if ($check == 0) {
			json(response(false, 400, 'data karyawan tidak ditemukan'));
		}
		$hapus = $this->db->where('id', $id)->delete('karyawan');
		if (!$hapus) {
			json(response(false, 500, 'gagal hapus data karyawan'));
		}
		json(response(true, 200, 'berhasil hapus data karyawan'));
	}
}
