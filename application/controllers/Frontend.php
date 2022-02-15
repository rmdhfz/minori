<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		$this->load->database();
		$data = [
			'karyawan' => $this->db->query("SELECT id, nip, nama FROM karyawan")->result()
		];
		$this->load->view('frontend/index', $data);
	}
	function karyawanData()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->data_karyawan();
	}
	function karyawanSimpan()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->simpan_karyawan();
	}
	function karyawanId()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->id_karyawan();
	}
	function karyawanEdit()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->edit_karyawan();
	}
	function karyawanHapus()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->hapus_karyawan();
	}
	// ------------------------------------------------------------------------
	function trainingData()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->data_training();
	}
	function trainingSimpan()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->simpan_training();
	}
	function trainingId()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->id_training();
	}
	function trainingEdit()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->edit_training();
	}
	function trainingHapus()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->hapus_training();
	}
	// ------------------------------------------------------------------------
	function trainingKaryawanData()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			http_response_code(405);
			return;
		}
		$this->load->model('model');
		$this->model->training_karyawan_data();
	}
}
