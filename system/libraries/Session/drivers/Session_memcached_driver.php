<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Session_memcached_driver extends CI_Session_driver implements SessionHandlerInterface {
	protected $_memcached;
	protected $_key_prefix = 'ci_session:';
	protected $_lock_key;
	public function __construct(&$params){
		parent::__construct($params);
		if (empty($this->_config['save_path'])){
			log_message('error', 'Session: No Memcached save path configured.');
		}
		if ($this->_config['match_ip'] === TRUE){
			$this->_key_prefix .= $_SERVER['REMOTE_ADDR'].':';
		}
	}
	public function open($save_path, $name){
		$this->_memcached = new Memcached();
		$this->_memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
		$server_list = array();
		foreach ($this->_memcached->getServerList() as $server){
			$server_list[] = $server['host'].':'.$server['port'];
		}
		if ( ! preg_match_all('#,?([^,:]+)\:(\d{1,5})(?:\:(\d+))?#', $this->_config['save_path'], $matches, PREG_SET_ORDER)){
			$this->_memcached = NULL;
			log_message('error', 'Session: Invalid Memcached save path format: '.$this->_config['save_path']);
			return $this->_fail();
		}
		foreach ($matches as $match){
			if (in_array($match[1].':'.$match[2], $server_list, TRUE)){
				log_message('debug', 'Session: Memcached server pool already has '.$match[1].':'.$match[2]);
				continue;
			}
			if ( ! $this->_memcached->addServer($match[1], $match[2], isset($match[3]) ? $match[3] : 0))
			{
				log_message('error', 'Could not add '.$match[1].':'.$match[2].' to Memcached server pool.');
			}
			else
			{
				$server_list[] = $match[1].':'.$match[2];
			}
		}

		if (empty($server_list))
		{
			log_message('error', 'Session: Memcached server pool is empty.');
			return $this->_fail();
		}

		return $this->_success;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
		if (isset($this->_memcached) && $this->_get_lock($session_id))
		{
			// Needed by write() to detect session_regenerate_id() calls
			$this->_session_id = $session_id;

			$session_data = (string) $this->_memcached->get($this->_key_prefix.$session_id);
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return $this->_fail();
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 *
	 * Writes (create / update) session data
	 *
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		if ( ! isset($this->_memcached, $this->_lock_key))
		{
			return $this->_fail();
		}
		// Was the ID regenerated?
		elseif ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return $this->_fail();
			}

			$this->_fingerprint = md5('');
			$this->_session_id = $session_id;
		}

		$key = $this->_key_prefix.$session_id;

		$this->_memcached->replace($this->_lock_key, time(), 300);
		if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
		{
			if ($this->_memcached->set($key, $session_data, $this->_config['expiration']))
			{
				$this->_fingerprint = $fingerprint;
				return $this->_success;
			}

			return $this->_fail();
		}
		elseif (
			$this->_memcached->touch($key, $this->_config['expiration'])
			OR ($this->_memcached->getResultCode() === Memcached::RES_NOTFOUND && $this->_memcached->set($key, $session_data, $this->_config['expiration']))
		)
		{
			return $this->_success;
		}

		return $this->_fail();
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Releases locks and closes connection.
	 *
	 * @return	bool
	 */
	public function close()
	{
		if (isset($this->_memcached))
		{
			$this->_release_lock();
			if ( ! $this->_memcached->quit())
			{
				return $this->_fail();
			}

			$this->_memcached = NULL;
			return $this->_success;
		}

		return $this->_fail();
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 *
	 * Destroys the current session.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		if (isset($this->_memcached, $this->_lock_key))
		{
			$this->_memcached->delete($this->_key_prefix.$session_id);
			$this->_cookie_destroy();
			return $this->_success;
		}

		return $this->_fail();
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 *
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		// Not necessary, Memcached takes care of that.
		return $this->_success;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 *
	 * Acquires an (emulated) lock.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		// PHP 7 reuses the SessionHandler object on regeneration,
		// so we need to check here if the lock key is for the
		// correct session ID.
		if ($this->_lock_key === $this->_key_prefix.$session_id.':lock')
		{
			if ( ! $this->_memcached->replace($this->_lock_key, time(), 300))
			{
				return ($this->_memcached->getResultCode() === Memcached::RES_NOTFOUND)
					? $this->_memcached->add($this->_lock_key, time(), 300)
					: FALSE;
			}
		}

		// 30 attempts to obtain a lock, in case another request already has it
		$lock_key = $this->_key_prefix.$session_id.':lock';
		$attempt = 0;
		do
		{
			if ($this->_memcached->get($lock_key))
			{
				sleep(1);
				continue;
			}

			$method = ($this->_memcached->getResultCode() === Memcached::RES_NOTFOUND) ? 'add' : 'set';
			if ( ! $this->_memcached->$method($lock_key, time(), 300))
			{
				log_message('error', 'Session: Error while trying to obtain lock for '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}
		while (++$attempt < 30);

		if ($attempt === 30)
		{
			log_message('error', 'Session: Unable to obtain lock for '.$this->_key_prefix.$session_id.' after 30 attempts, aborting.');
			return FALSE;
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 *
	 * Releases a previously acquired lock
	 *
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if (isset($this->_memcached, $this->_lock_key) && $this->_lock)
		{
			if ( ! $this->_memcached->delete($this->_lock_key) && $this->_memcached->getResultCode() !== Memcached::RES_NOTFOUND)
			{
				log_message('error', 'Session: Error while trying to free lock for '.$this->_lock_key);
				return FALSE;
			}

			$this->_lock_key = NULL;
			$this->_lock = FALSE;
		}

		return TRUE;
	}
}
