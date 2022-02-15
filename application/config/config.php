<?php
defined('BASEPATH') OR exit('No direct script access allowed');

@$config['base_url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
@$config['base_url'] .= "://" . $_SERVER['HTTP_HOST'];
@$config['base_url'] .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

$config['index_page'] = '';
$config['uri_protocol']	= 'REQUEST_URL';
$config['url_suffix'] = '';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-=';
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['allow_get_array'] = TRUE;
$config['log_threshold'] = TRUE;
$config['log_path'] = APPPATH.'/logs/';
$config['log_file_extension'] = 'txt';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'd-m-Y H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = 'HQ=?cUP@Q3p%7BEWqQdss-vX25eNWthC@d*4_#!';
$config['sess_driver'] = NULL;
$config['sess_cookie_name'] = 'minori';
// 1. Session expiration satuannya adalah detik.
// 2. Jumlah 2678400 adalah = 31 hari. (sumber : convert in google.com - second to day)
$config['sess_expiration'] = 2678400;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = TRUE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = TRUE;
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = TRUE;
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf';
$config['csrf_cookie_name'] = 'csrf';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = FALSE;
$config['csrf_exclude_uris'] = [];
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
