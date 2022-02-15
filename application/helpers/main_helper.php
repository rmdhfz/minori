<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('rupiah')){
    function rupiah($number){
        $code = number_format($number,0,',','.');
        return $code;
    }
}

if ( ! function_exists('weekOfMonth')) {
    function weekOfMonth($when = null) {
        if ($when === null) $when = time();
        $week = date('W', $when); // note that ISO weeks start on Monday
        $firstWeekOfMonth = date('W', strtotime(date('Y-m-01', $when)));
        return 1 + ($week < $firstWeekOfMonth ? $week : $week - $firstWeekOfMonth);
    }
}

if ( ! function_exists('getWeeks')) {
    function getWeeks($date, $rollover){
        $cut = substr($date, 0, 8);
        $daylen = 86400;
        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;
        $weeks = 1;
        for ($i = 1; $i <= $elapsed; $i++){
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }
        return $weeks;
    }

}

if ( ! function_exists('enSHA')) {
  function enSHA($data){
    echo $hmac = hash_hmac('SHA512', base64_encode($data), config_item('encryption_key'));
    return $hmac;
  }
}
if ( ! function_exists('getlog')) {
  function getlog(){
    $now = date('d-m-Y');
    $get = file_get_contents(realpath(__DIR__.'../../../debug/log-'.$now.'.txt'));
  }
}
if ( ! function_exists('safeURL')) {
  function safeURL($url){
    echo $safe = base64_encode($url);
    return $safe;
  }
}
if ( ! function_exists('intToMonth') ) {
  function intToMonth($monthNum){
    $dateObj   = DateTime::createFromFormat('!m', '0'.$monthNum);
    $monthName = $dateObj->format('F');
    return $monthName;
  }
}
if ( ! function_exists('secToHR')) {
  function secToHR($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds / 60) % 60);
    $seconds = $seconds % 60;
    return $hours > 0 ? "$hours hours, $minutes min" : ($minutes > 0 ? "$minutes min, $seconds sec" : "$seconds sec");
  }
}

if ( ! function_exists('readURL')) {
  function readURL($url){
    $read = base64_decode($url);
    return $read;
  }
}
if ( ! function_exists('setheader')) {
  function setheader(){
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
    header("Access-Control-Allow-Headers: ".site_url());
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Origin: ".site_url());
    header("Access-Control-Allow-Credentials: true");
    header("Accept-Ranges: bytes");
    header("Accept-Encoding: gzip, deflate");
    // header("Cache-Control: no-store, no-cache, must-revalidate");
    header('Createdby: Hafiz Ramadhan');
    header("Strict-Transport-Security: max-age=31536000");
    header("Access-Control-Allow-Origin: ".site_url());
    header("Origin: ".site_url());
    header("Server: **********");
    header("X-Powered-By: **********");
    header("Timing-Allow-Origin: ".site_url());
    header("ETag: 33a64df551425fcc55e4d42a148795d9f25f89d4");
    header("If-Match: 33a64df551425fcc55e4d42a148795d9f25f89d4");
    header("X-DNS-Prefetch-Control: ON");
    header("X-Frame-Options: sameorigin");
    header("DNT: 1");
    header("Vary: Origin");
  }
}
if ( ! function_exists('setSecurity')) {
  function setSecurity(){
    $CI =& get_instance();
    $origin = $CI->input->server('origin');
    if ($origin !== "" || $origin !== NULL) {
      $allowed = array(site_url()); 
      if(in_array($origin, $allowed)){

      }else{
        var_dump($origin); die();
      }   
    }
  }
}
if ( ! function_exists('BulanToRomawi')) {
  function BulanToRomawi($bulanAngka){
    if ($bulanAngka == '01') {
        $bulan = 'I';
    }elseif ($bulanAngka == '02') {
        $bulan = 'II';
    }elseif ($bulanAngka == '03') {
        $bulan = 'III';
    }elseif ($bulanAngka == '04') {
        $bulan = 'IV';
    }elseif ($bulanAngka == '05') {
        $bulan = 'V';
    }elseif ($bulanAngka == '06') {
        $bulan = 'VI';
    }elseif ($bulanAngka == '07') {
        $bulan = 'VII';
    }elseif ($bulanAngka == '08') {
        $bulan = 'VIII';
    }elseif ($bulanAngka == '09') {
        $bulan = 'IX';
    }elseif ($bulanAngka == '10') {
        $bulan = 'X';
    }elseif ($bulanAngka == '11') {
        $bulan = 'XI';
    }elseif ($bulanAngka == '12') {
        $bulan = 'XII';
    }else{
      $bulan = 'Invalid Bulan';
    }
    return $bulan;
  }
}
if ( ! function_exists('change_format_date')) {
  function change_format_date($date){
      if ($date) {
          $code = date('d-m-Y H:i:s', strtotime(str_replace('-', '/', $date)));
          return $code; 
      }else{
          return '-';
      }
  }
}
if ( ! function_exists('formatBytes')) {
  function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
  }
}
if ( ! function_exists('isajax')) {
  function isajax(){
    $CI =& get_instance();
    if (ispost()) {
      if (!$CI->input->is_ajax_request()) { 
        http_response_code(401);
      }
    }
  }
}
if ( ! function_exists('ispost') ) {
  function ispost(){
      $RequestMethod = $_SERVER['REQUEST_METHOD'];
      if ($RequestMethod === "GET") {
        exit(http_response_code(400)); die();
      }
  }
}
if ( ! function_exists('generateCsrf') ) {
  function generateCsrf(){
    $CI =& get_instance();
    $response = [ 'hash' => $CI->security->get_csrf_hash() ];
    JSON($response);
  }
}
if ( ! function_exists('action')) {
  function action($function){
    $code = base_url('backend/'.$function.'');
    return $code;
  }
}
if ( ! function_exists('is_logged_in')){
  function is_logged_in() {
      $CI =& get_instance(); 
      $CI->load->library('session');
      return $CI->session->userdata('is_login');
  }
}
if ( ! function_exists('JSON')) {
  function json($data=''){
    $CI =& get_instance();
    $CI->output->set_content_type('application/json', 'utf-8')->set_output(JSON_encode($data, JSON_PRETTY_PRINT))->_display();
    exit;
  }
}
if ( ! function_exists('cek') ) {
  function cek(){
    $CI =& get_instance();
    $code = $CI->session->userdata('sudah');
    return $code;
  }
}
if ( ! function_exists('verifikasi')) {
  function verifikasi(){
    $CI =& get_instance();
    if ( ! $CI->session->userdata('nama', 'status')) {
      redirect(base_url());
    }
  }
}
if ( ! function_exists('interpreter')) {
    function interpreter($detail){ 
      $CI =& get_instance();
      $code = $CI->load->view('backend/index', $detail);
      return $code;
  }
}
/*this helper for multiple database, sementara di nonaktifkan*/
if ( ! function_exists('query')) {
    function query($sql=[], $server){
      $CI =& get_instance();
      $validate = $server === null ? "default" : $server;
      $code = $CI->load->database($validate, TRUE);
      return $code->query($sql);
  }
}

if ( ! function_exists('get')) {
    function get(string $table, $server){
      $CI =& get_instance();
      $validate = $server === null ? "default" : $server;
      $code = $CI->load->database($validate, TRUE);
      $code = $code->get($table);
      return $code;
  }
}

if ( ! function_exists('where')) {
  function where($data, $where = '', $server){
    $CI =& get_instance();
    $validate = $server === null ? "default" : $server;
    $db = $CI->load->database($validate, TRUE);
    $code = $db->where($data, $where);
    return $code;
  }
}

if ( ! function_exists('save')) {
    function save(string $table, $data=[], $server){
      $CI =& get_instance();
      $validate = $server === null ? "default" : $server;
      $db = $CI->load->database($validate, TRUE);
      $code = $db->insert($table, $data);
      return $code;
  }
}
if ( ! function_exists('update')) {
  function update(string $table, $data=[], int $id, $server){
    $CI =& get_instance();
    $validate = $server === null ? "default" : $server;
    $db = $CI->load->database($validate, TRUE);
    $code = $db->where('id', $id);
    $code = $db->update($table, $data);
    return $code;
  }
}
if ( ! function_exists('count_all_results')) {
  function count_all_results(string $table){
    $CI =& get_instance();
    $code = $CI->db->count_all_results($table);
    return $code;
  }
}
if ( ! function_exists('post')) {
  function post($data){
    $CI =& get_instance();
    $code = $CI->security->xss_clean(stripcslashes(htmlspecialchars(htmlentities($CI->input->post($data, TRUE)))));
    return $code;
  }
}
if ( ! function_exists('load')) {
    function load($folder, $data=''){
      $CI = & get_instance();
      $code = $CI->load->view($folder, $data);
      return $code;
  }
}

if ( ! function_exists('loadmodel')) {
  function loadmodel(string $modelname, string $as){
    $CI =& get_instance();
    $code = $CI->load->model($modelname, $as);
    return $code;
  }
}
if ( ! function_exists('security')) {
  function security(string $function){
    $CI =& get_instance();
    $code = $CI->security->$function;
    return $code;
  }
}
if ( ! function_exists('get_ip_address')) {
  function get_ip_address() {
    $code = getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR');
    return $code;
  }
}
if ( ! function_exists('check_internet_connection')) {
  function check_internet_connection() {
    return checkdnsrr('google.co.id');
  }
}
if ( ! function_exists('session')) {
    function session(string $session){
      $CI =& get_instance();
      $CI->load->library('session');
      $code = $CI->session->userdata($session);
      return $code;
    }
}
if ( ! function_exists('allowed_type')) {
    function allowed_type(string $data){
      $fileNameParts = explode(".", $data);
      $fileExtension = end($fileNameParts);
      $fileExtension = strtolower($fileExtension);
      return $fileExtension;
    }
}
if ( ! function_exists('verifyInt')) {
  function verifyInt($p){
      if (!is_numeric($p)) {
        json(var_export($p, true)." is not numeric".PHP_EOL);
      }else{
        return $p;
      }
  }
}
if ( ! function_exists('verifyEmail')) {
  function verifyEmail($em){
    if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
      return true;
    } else {
      json(var_export($em, true)." is not valid email".PHP_EOL);
    }
  }
}