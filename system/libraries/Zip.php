<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Zip {
	public $zipdata = '';
	public $directory = '';
	public $entries = 0;
	public $file_num = 0;
	public $offset = 0;
	public $now;
	public $compression_level = 2;
	protected static $func_overload;
	public function __construct(){
		isset(self::$func_overload) OR self::$func_overload = (extension_loaded('mbstring') && ini_get('mbstring.func_overload'));
		$this->now = time();
		log_message('info', 'Zip Compression Class Initialized');
	}
	public function add_dir($directory){
		foreach ((array) $directory as $dir){
			if ( ! preg_match('|.+/$|', $dir)){
				$dir .= '/';
			}
			$dir_time = $this->_get_mod_time($dir);
			$this->_add_dir($dir, $dir_time['file_mtime'], $dir_time['file_mdate']);
		}
	}
	protected function _get_mod_time($dir){
		$date = file_exists($dir) ? getdate(filemtime($dir)) : getdate($this->now);
		return array(
			'file_mtime' => ($date['hours'] << 11) + ($date['minutes'] << 5) + $date['seconds'] / 2,
			'file_mdate' => (($date['year'] - 1980) << 9) + ($date['mon'] << 5) + $date['mday']
		);
	}
	protected function _add_dir($dir, $file_mtime, $file_mdate){
		$dir = str_replace('\\', '/', $dir);
		$this->zipdata .=
			"\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V', 0) 
			.pack('V', 0) 
			.pack('V', 0) 
			.pack('v', self::strlen($dir))
			.pack('v', 0) 
			.$dir
			.pack('V', 0) 
			.pack('V', 0) 
			.pack('V', 0); 
		$this->directory .=
			"\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V',0) // crc32
			.pack('V',0) // compressed filesize
			.pack('V',0) // uncompressed filesize
			.pack('v', self::strlen($dir)) // length of pathname
			.pack('v', 0) // extra field length
			.pack('v', 0) // file comment length
			.pack('v', 0) // disk number start
			.pack('v', 0) // internal file attributes
			.pack('V', 16) // external file attributes - 'directory' bit set
			.pack('V', $this->offset) // relative offset of local header
			.$dir;

		$this->offset = self::strlen($this->zipdata);
		$this->entries++;
	}
	public function add_data($filepath, $data = NULL){
		if (is_array($filepath)){
			foreach ($filepath as $path => $data){
				$file_data = $this->_get_mod_time($path);
				$this->_add_data($path, $data, $file_data['file_mtime'], $file_data['file_mdate']);
			}
		}else{
			$file_data = $this->_get_mod_time($filepath);
			$this->_add_data($filepath, $data, $file_data['file_mtime'], $file_data['file_mdate']);
		}
	}
	protected function _add_data($filepath, $data, $file_mtime, $file_mdate){
		$filepath = str_replace('\\', '/', $filepath);
		$uncompressed_size = self::strlen($data);
		$crc32  = crc32($data);
		$gzdata = self::substr(gzcompress($data, $this->compression_level), 2, -4);
		$compressed_size = self::strlen($gzdata);
		$this->zipdata .=
			"\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V', $crc32)
			.pack('V', $compressed_size)
			.pack('V', $uncompressed_size)
			.pack('v', self::strlen($filepath)) // length of filename
			.pack('v', 0) // extra field length
			.$filepath
			.$gzdata; // "file data" segment
		$this->directory .=
			"\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V', $crc32)
			.pack('V', $compressed_size)
			.pack('V', $uncompressed_size)
			.pack('v', self::strlen($filepath)) // length of filename
			.pack('v', 0) // extra field length
			.pack('v', 0) // file comment length
			.pack('v', 0) // disk number start
			.pack('v', 0) // internal file attributes
			.pack('V', 32) // external file attributes - 'archive' bit set
			.pack('V', $this->offset) // relative offset of local header
			.$filepath;
		$this->offset = self::strlen($this->zipdata);
		$this->entries++;
		$this->file_num++;
	}
	public function read_file($path, $archive_filepath = FALSE){
		if (file_exists($path) && FALSE !== ($data = file_get_contents($path))){
			if (is_string($archive_filepath)){
				$name = str_replace('\\', '/', $archive_filepath);
			}else{
				$name = str_replace('\\', '/', $path);
				if ($archive_filepath === FALSE){
					$name = preg_replace('|.*/(.+)|', '\\1', $name);
				}
			}
			$this->add_data($name, $data);
			return TRUE;
		}
		return FALSE;
	}
	public function read_dir($path, $preserve_filepath = TRUE, $root_path = NULL){
		$path = rtrim($path, '/\\').DIRECTORY_SEPARATOR;
		if ( ! $fp = @opendir($path)){
			return FALSE;
		}
		if ($root_path === NULL){
			$root_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, dirname($path)).DIRECTORY_SEPARATOR;
		}
		while (FALSE !== ($file = readdir($fp))){
			if ($file[0] === '.'){
				continue;
			}
			if (is_dir($path.$file)){
				$this->read_dir($path.$file.DIRECTORY_SEPARATOR, $preserve_filepath, $root_path);
			}
			elseif (FALSE !== ($data = file_get_contents($path.$file))){
				$name = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
				if ($preserve_filepath === FALSE){
					$name = str_replace($root_path, '', $name);
				}
				$this->add_data($name.$file, $data);
			}
		}
		closedir($fp);
		return TRUE;
	}
	public function get_zip(){
		if ($this->entries === 0){
			return FALSE;
		}

		return $this->zipdata
			.$this->directory."\x50\x4b\x05\x06\x00\x00\x00\x00"
			.pack('v', $this->entries) // total # of entries "on this disk"
			.pack('v', $this->entries) // total # of entries overall
			.pack('V', self::strlen($this->directory)) // size of central dir
			.pack('V', self::strlen($this->zipdata)) // offset to start of central dir
			."\x00\x00"; // .zip file comment length
	}

	// --------------------------------------------------------------------

	/**
	 * Write File to the specified directory
	 *
	 * Lets you write a file
	 *
	 * @param	string	$filepath	the file name
	 * @return	bool
	 */
	public function archive($filepath)
	{
		if ( ! ($fp = @fopen($filepath, 'w+b')))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $data = $this->get_zip(), $length = self::strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, self::substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}

	// --------------------------------------------------------------------

	/**
	 * Download
	 *
	 * @param	string	$filename	the file name
	 * @return	void
	 */
	public function download($filename = 'backup.zip')
	{
		if ( ! preg_match('|.+?\.zip$|', $filename))
		{
			$filename .= '.zip';
		}

		get_instance()->load->helper('download');
		$get_zip = $this->get_zip();
		$zip_content =& $get_zip;

		force_download($filename, $zip_content);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Data
	 *
	 * Lets you clear current zip data. Useful if you need to create
	 * multiple zips with different data.
	 *
	 * @return	CI_Zip
	 */
	public function clear_data()
	{
		$this->zipdata = '';
		$this->directory = '';
		$this->entries = 0;
		$this->file_num = 0;
		$this->offset = 0;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */
	protected static function strlen($str)
	{
		return (self::$func_overload)
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */
	protected static function substr($str, $start, $length = NULL)
	{
		if (self::$func_overload){
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}
