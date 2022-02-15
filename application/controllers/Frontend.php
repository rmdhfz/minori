<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		$this->load->view('frontend/index');
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
}
