<?php

    header("Cache-Control: must-revalidate, public, max-age=31536000");
    date_default_timezone_set('Asia/Jakarta');

	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
	// define('ENVIRONMENT', 'production');

	switch (ENVIRONMENT){
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;
	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>=')){
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}else{ error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE); }
	break;
	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); die(1);
	}

	$system = 'engine';
	$app = 'app';
	$view = '';

	if (defined('STDIN')){ chdir(dirname(__FILE__)); }
	if (($_temp = realpath($system)) !== FALSE){ $system = $_temp.DIRECTORY_SEPARATOR; }
	else { $system = strtr( rtrim($system, '/\\'), '/\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR; }
	if ( ! is_dir($system)){ header('HTTP/1.1 503 Service Unavailable.', TRUE, 503); echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); }

	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	define('BASEPATH', $system);
	define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
	define('SYSDIR', basename(BASEPATH));

	if (is_dir($app)) {
		if (($_temp = realpath($app)) !== FALSE){ $app = $_temp; }
		else {
			$app = strtr( rtrim($app, '/\\'), '/\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR );
		}
	} elseif (is_dir(BASEPATH.$app.DIRECTORY_SEPARATOR)) { $app = BASEPATH.strtr( trim($app, '/\\'), '/\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR );}
	else { header('HTTP/1.1 503 Service Unavailable.', TRUE, 503); echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF; exit(3); }
	define('APPPATH', $app.DIRECTORY_SEPARATOR);
	if ( ! isset($view[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR)) { $view = APPPATH.'views'; }
	elseif (is_dir($view)) { if (($_temp = realpath($view)) !== FALSE){ $view = $_temp; }
		else { $view = strtr( rtrim($view, '/\\'), '/\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR ); } }
	elseif (is_dir(APPPATH.$view.DIRECTORY_SEPARATOR)) {
		$view = APPPATH.strtr( trim($view, '/\\'), '/\\', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR ); }
	else { header('HTTP/1.1 503 Service Unavailable.', TRUE, 503); echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF; exit(3); } define('VIEWPATH', $view.DIRECTORY_SEPARATOR); require_once BASEPATH.'core/CodeIgniter.php';
