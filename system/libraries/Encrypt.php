<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Encrypt {
	public $encryption_key		= '';
	protected $_hash_type		= 'sha1';
	protected $_mcrypt_exists	= FALSE;
	protected $_mcrypt_cipher;
	protected $_mcrypt_mode;
	public function __construct(){
		if (($this->_mcrypt_exists = function_exists('mcrypt_encrypt')) === FALSE){
			show_error('The Encrypt library requires the Mcrypt extension.');
		}
		log_message('info', 'Encrypt Class Initialized');
	}
	public function get_key($key = ''){
		if ($key === ''){
			if ($this->encryption_key !== ''){
				return $this->encryption_key;
			}
			$key = config_item('encryption_key');
			if ( ! self::strlen($key)){
				show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
			}
		}
		return md5($key);
	}
	public function set_key($key = ''){
		$this->encryption_key = $key;
		return $this;
	}
	public function encode($string, $key = ''){
		return base64_encode($this->mcrypt_encode($string, $this->get_key($key)));
	}
	public function decode($string, $key = ''){
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string) OR base64_encode(base64_decode($string)) !== $string){
			return FALSE;
		}
		return $this->mcrypt_decode(base64_decode($string), $this->get_key($key));
	}
	public function encode_from_legacy($string, $legacy_mode = MCRYPT_MODE_ECB, $key = ''){
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string)){
			return FALSE;
		}
		$current_mode = $this->_get_mode();
		$this->set_mode($legacy_mode);
		$key = $this->get_key($key);
		$dec = base64_decode($string);
		if (($dec = $this->mcrypt_decode($dec, $key)) === FALSE){
			$this->set_mode($current_mode);
			return FALSE;
		}

		$dec = $this->_xor_decode($dec, $key);

		// set the mcrypt mode back to what it should be, typically MCRYPT_MODE_CBC
		$this->set_mode($current_mode);

		// and re-encode
		return base64_encode($this->mcrypt_encode($dec, $key));
	}

	// --------------------------------------------------------------------

	/**
	 * XOR Decode
	 *
	 * Takes an encoded string and key as input and generates the
	 * plain-text original message
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);

		$dec = '';
		for ($i = 0, $l = self::strlen($string); $i < $l; $i++)
		{
			$dec .= ($string[$i++] ^ $string[$i]);
		}

		return $dec;
	}

	// --------------------------------------------------------------------

	/**
	 * XOR key + string Combiner
	 *
	 * Takes a string and key as input and computes the difference using XOR
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';

		for ($i = 0, $ls = self::strlen($string), $lh = self::strlen($hash); $i < $ls; $i++)
		{
			$str .= $string[$i] ^ $hash[($i % $lh)];
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Encrypt using Mcrypt
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_encode($data, $key)
	{
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_DEV_URANDOM);
		return $this->_add_cipher_noise($init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), $key);
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt using Mcrypt
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_decode($data, $key)
	{
		$data = $this->_remove_cipher_noise($data, $key);
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());

		if ($init_size > self::strlen($data))
		{
			return FALSE;
		}

		$init_vect = self::substr($data, 0, $init_size);
		$data      = self::substr($data, $init_size);

		return rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
	}

	// --------------------------------------------------------------------

	/**
	 * Adds permuted noise to the IV + encrypted data to protect
	 * against Man-in-the-middle attacks on CBC mode ciphers
	 * http://www.ciphersbyritter.com/GLOSSARY.HTM#IV
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _add_cipher_noise($data, $key)
	{
		$key = $this->hash($key);
		$str = '';

		for ($i = 0, $j = 0, $ld = self::strlen($data), $lk = self::strlen($key); $i < $ld; ++$i, ++$j)
		{
			if ($j >= $lk)
			{
				$j = 0;
			}

			$str .= chr((ord($data[$i]) + ord($key[$j])) % 256);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Removes permuted noise from the IV + encrypted data, reversing
	 * _add_cipher_noise()
	 *
	 * Function description
	 *
	 * @param	string	$data
	 * @param	string	$key
	 * @return	string
	 */
	protected function _remove_cipher_noise($data, $key)
	{
		$key = $this->hash($key);
		$str = '';

		for ($i = 0, $j = 0, $ld = self::strlen($data), $lk = self::strlen($key); $i < $ld; ++$i, ++$j)
		{
			if ($j >= $lk)
			{
				$j = 0;
			}

			$temp = ord($data[$i]) - ord($key[$j]);

			if ($temp < 0)
			{
				$temp += 256;
			}

			$str .= chr($temp);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Cipher
	 *
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_cipher($cipher)
	{
		$this->_mcrypt_cipher = $cipher;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Mode
	 *
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_mode($mode)
	{
		$this->_mcrypt_mode = $mode;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt cipher Value
	 *
	 * @return	int
	 */
	protected function _get_cipher()
	{
		if ($this->_mcrypt_cipher === NULL)
		{
			return $this->_mcrypt_cipher = MCRYPT_RIJNDAEL_256;
		}

		return $this->_mcrypt_cipher;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt Mode Value
	 *
	 * @return	int
	 */
	protected function _get_mode()
	{
		if ($this->_mcrypt_mode === NULL)
		{
			return $this->_mcrypt_mode = MCRYPT_MODE_CBC;
		}

		return $this->_mcrypt_mode;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Hash type
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_hash($type = 'sha1')
	{
		$this->_hash_type = in_array($type, hash_algos()) ? $type : 'sha1';
	}

	// --------------------------------------------------------------------

	/**
	 * Hash encode a string
	 *
	 * @param	string
	 * @return	string
	 */
	public function hash($str)
	{
		return hash($this->_hash_type, $str);
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
		return defined('MB_OVERLOAD_STRING')
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
		if (defined('MB_OVERLOAD_STRING'))
		{
			// mb_substr($str, $start, null, '8bit') returns an empty
			// string on PHP 5.3
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}
