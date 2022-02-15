<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Parser {
	public $l_delim = '{';
	public $r_delim = '}';
	protected $CI;
	public function __construct(){
		$this->CI =& get_instance();
		log_message('info', 'Parser Class Initialized');
	}
	public function parse($template, $data, $return = FALSE){
		$template = $this->CI->load->view($template, $data, TRUE);
		return $this->_parse($template, $data, $return);
	}
	public function parse_string($template, $data, $return = FALSE){
		return $this->_parse($template, $data, $return);
	}
	protected function _parse($template, $data, $return = FALSE){
		if ($template === ''){
			return FALSE;
		}
		$replace = array();
		foreach ($data as $key => $val){
			$replace = array_merge(
				$replace,
				is_array($val)
					? $this->_parse_pair($key, $val, $template)
					: $this->_parse_single($key, (string) $val, $template)
			);
		}
		unset($data);
		$template = strtr($template, $replace);
		if ($return === FALSE){
			$this->CI->output->append_output($template);
		}
		return $template;
	}
	public function set_delimiters($l = '{', $r = '}'){
		$this->l_delim = $l;
		$this->r_delim = $r;
	}
	protected function _parse_single($key, $val, $string){
		return array($this->l_delim.$key.$this->r_delim => (string) $val);
	}
	protected function _parse_pair($variable, $data, $string){
		$replace = array();
		preg_match_all(
			'#'.preg_quote($this->l_delim.$variable.$this->r_delim).'(.+?)'.preg_quote($this->l_delim.'/'.$variable.$this->r_delim).'#s',
			$string,
			$matches,
			PREG_SET_ORDER
		);
		foreach ($matches as $match){
			$str = '';
			foreach ($data as $row){
				$temp = array();
				foreach ($row as $key => $val){
					if (is_array($val)){
						$pair = $this->_parse_pair($key, $val, $match[1]);
						if ( ! empty($pair)){
							$temp = array_merge($temp, $pair);
						}
						continue;
					}
					$temp[$this->l_delim.$key.$this->r_delim] = $val;
				}
				$str .= strtr($match[1], $temp);
			}
			$replace[$match[0]] = $str;
		}
		return $replace;
	}
}