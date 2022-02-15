<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_FTP {
	public $hostname = '';
	public $username = '';
	public $password = '';
	public $port = 21;
	public $passive = TRUE;
	public $debug = FALSE;
	protected $conn_id;
	public function __construct($config = array()){
		empty($config) OR $this->initialize($config);
		log_message('info', 'FTP Class Initialized');
	}
	public function initialize($config = array()){
		foreach ($config as $key => $val){
			if (isset($this->$key)){
				$this->$key = $val;
			}
		}
		$this->hostname = preg_replace('|.+?://|', '', $this->hostname);
	}
	public function connect($config = array()){
		if (count($config) > 0){
			$this->initialize($config);
		}
		if (FALSE === ($this->conn_id = @ftp_connect($this->hostname, $this->port))){
			if ($this->debug === TRUE){
				$this->_error('ftp_unable_to_connect');
			}
			return FALSE;
		}
		if ( ! $this->_login()){
			if ($this->debug === TRUE){
				$this->_error('ftp_unable_to_login');
			}
			return FALSE;
		}
		if ($this->passive === TRUE){
			ftp_pasv($this->conn_id, TRUE);
		}
		return TRUE;
	}
	protected function _login(){
		return @ftp_login($this->conn_id, $this->username, $this->password);
	}
	protected function _is_conn(){
		if ( ! is_resource($this->conn_id)){
			if ($this->debug === TRUE){
				$this->_error('ftp_no_connection');
			}
			return FALSE;
		}
		return TRUE;
	}
	public function changedir($path, $suppress_debug = FALSE){
		if ( ! $this->_is_conn()){
			return FALSE;
		}
		$result = @ftp_chdir($this->conn_id, $path);
		if ($result === FALSE){
			if ($this->debug === TRUE && $suppress_debug === FALSE){
				$this->_error('ftp_unable_to_changedir');
			}
			return FALSE;
		}
		return TRUE;
	}
	public function mkdir($path, $permissions = NULL){
		if ($path === '' OR ! $this->_is_conn()){
			return FALSE;
		}
		$result = @ftp_mkdir($this->conn_id, $path);
		if ($result === FALSE){
			if ($this->debug === TRUE){
				$this->_error('ftp_unable_to_mkdir');
			}
			return FALSE;
		}
		if ($permissions !== NULL){
			$this->chmod($path, (int) $permissions);
		}
		return TRUE;
	}
	public function upload($locpath, $rempath, $mode = 'auto', $permissions = NULL){
		if ( ! $this->_is_conn()){
			return FALSE;
		}
		if ( ! file_exists($locpath)){
			$this->_error('ftp_no_source_file');
			return FALSE;
		}
		if ($mode === 'auto')
		{
			// Get the file extension so we can set the upload type
			$ext = $this->_getext($locpath);
			$mode = $this->_settype($ext);
		}

		$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

		$result = @ftp_put($this->conn_id, $rempath, $locpath, $mode);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_upload');
			}

			return FALSE;
		}

		// Set file permissions if needed
		if ($permissions !== NULL)
		{
			$this->chmod($rempath, (int) $permissions);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Download a file from a remote server to the local server
	 *
	 * @param	string	$rempath
	 * @param	string	$locpath
	 * @param	string	$mode
	 * @return	bool
	 */
	public function download($rempath, $locpath, $mode = 'auto')
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Set the mode if not specified
		if ($mode === 'auto')
		{
			// Get the file extension so we can set the upload type
			$ext = $this->_getext($rempath);
			$mode = $this->_settype($ext);
		}

		$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

		$result = @ftp_get($this->conn_id, $locpath, $rempath, $mode);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_download');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Rename (or move) a file
	 *
	 * @param	string	$old_file
	 * @param	string	$new_file
	 * @param	bool	$move
	 * @return	bool
	 */
	public function rename($old_file, $new_file, $move = FALSE)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		$result = @ftp_rename($this->conn_id, $old_file, $new_file);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_'.($move === FALSE ? 'rename' : 'move'));
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Move a file
	 *
	 * @param	string	$old_file
	 * @param	string	$new_file
	 * @return	bool
	 */
	public function move($old_file, $new_file)
	{
		return $this->rename($old_file, $new_file, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Rename (or move) a file
	 *
	 * @param	string	$filepath
	 * @return	bool
	 */
	public function delete_file($filepath)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		$result = @ftp_delete($this->conn_id, $filepath);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_delete');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a folder and recursively delete everything (including sub-folders)
	 * contained within it.
	 *
	 * @param	string	$filepath
	 * @return	bool
	 */
	public function delete_dir($filepath)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Add a trailing slash to the file path if needed
		$filepath = preg_replace('/(.+?)\/*$/', '\\1/', $filepath);

		$list = $this->list_files($filepath);
		if ( ! empty($list))
		{
			for ($i = 0, $c = count($list); $i < $c; $i++)
			{
				// If we can't delete the item it's probably a directory,
				// so we'll recursively call delete_dir()
				if ( ! preg_match('#/\.\.?$#', $list[$i]) && ! @ftp_delete($this->conn_id, $list[$i]))
				{
					$this->delete_dir($filepath.$list[$i]);
				}
			}
		}

		if (@ftp_rmdir($this->conn_id, $filepath) === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_delete');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set file permissions
	 *
	 * @param	string	$path	File path
	 * @param	int	$perm	Permissions
	 * @return	bool
	 */
	public function chmod($path, $perm)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		if (@ftp_chmod($this->conn_id, $perm, $path) === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_chmod');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * FTP List files in the specified directory
	 *
	 * @param	string	$path
	 * @return	array
	 */
	public function list_files($path = '.')
	{
		return $this->_is_conn()
			? ftp_nlist($this->conn_id, $path)
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read a directory and recreate it remotely
	 *
	 * This function recursively reads a folder and everything it contains
	 * (including sub-folders) and creates a mirror via FTP based on it.
	 * Whatever the directory structure of the original file path will be
	 * recreated on the server.
	 *
	 * @param	string	$locpath	Path to source with trailing slash
	 * @param	string	$rempath	Path to destination - include the base folder with trailing slash
	 * @return	bool
	 */
	public function mirror($locpath, $rempath)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Open the local file path
		if ($fp = @opendir($locpath))
		{
			// Attempt to open the remote file path and try to create it, if it doesn't exist
			if ( ! $this->changedir($rempath, TRUE) && ( ! $this->mkdir($rempath) OR ! $this->changedir($rempath)))
			{
				return FALSE;
			}

			// Recursively read the local directory
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($locpath.$file) && $file[0] !== '.')
				{
					$this->mirror($locpath.$file.'/', $rempath.$file.'/');
				}
				elseif ($file[0] !== '.')
				{
					// Get the file extension so we can se the upload type
					$ext = $this->_getext($file);
					$mode = $this->_settype($ext);

					$this->upload($locpath.$file, $rempath.$file, $mode);
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Extract the file extension
	 *
	 * @param	string	$filename
	 * @return	string
	 */
	protected function _getext($filename)
	{
		return (($dot = strrpos($filename, '.')) === FALSE)
			? 'txt'
			: substr($filename, $dot + 1);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the upload type
	 *
	 * @param	string	$ext	Filename extension
	 * @return	string
	 */
	protected function _settype($ext)
	{
		return in_array($ext, array('txt', 'text', 'php', 'phps', 'php4', 'js', 'css', 'htm', 'html', 'phtml', 'shtml', 'log', 'xml'), TRUE)
			? 'ascii'
			: 'binary';
	}

	// ------------------------------------------------------------------------

	/**
	 * Close the connection
	 *
	 * @return	bool
	 */
	public function close()
	{
		return $this->_is_conn()
			? @ftp_close($this->conn_id)
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Display error message
	 *
	 * @param	string	$line
	 * @return	void
	 */
	protected function _error($line)
	{
		$CI =& get_instance();
		$CI->lang->load('ftp');
		show_error($CI->lang->line($line));
	}

}
