<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route = [
	'404_override' => '',
	// ------------------------------------------------------------------------
	'default_controller'	=> 'Frontend',
	'karyawan'				=> 'Frontend/karyawan',
	'karyawan/data'			=> 'Frontend/karyawanData',
	'karyawan/simpan'		=> 'Frontend/karyawanSimpan',
	'karyawan/id'			=> 'Frontend/karyawanId',
	'karyawan/edit'			=> 'Frontend/karyawanEdit',
	'karyawan/hapus'		=> 'Frontend/karyawanHapus',

	'training'				=> 'Frontend/training',
	'training/simpan'		=> 'Frontend/trainingSimpan',
	'training/id'			=> 'Frontend/trainingId',
	'training/edit'			=> 'Frontend/trainingEdit',
	'training/hapus'		=> 'Frontend/trainingHapus',
];
