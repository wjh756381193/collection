<?php
/**
 * Author:wjh
 * Date:2015-01-09 03:23
 * $array:Ҫ���б��������
 * @param :$array ���������� ��ʱ��json_encode()����������������
 */
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }

        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}

    /**************************************************************
 	 *
 	 *  ������ת��ΪJSON�ַ������������ģ�
 	 *  @param  array   $array      Ҫת��������
 	 *  @return string      ת���õ���json�ַ���
 	 *  @access public
 	 *
 	 ************************************************************
     */
function JSON($array) {
    arrayRecursive($array, 'urlencode', true);
    $json = json_encode($array);
    return urldecode($json);
}
/**
 * Create UUID
 * @param $prefix
 * Author:wjh
 * Date:2014-12-01
 * @return string
 */
function create_uuid($prefix = ""){    //����ָ��ǰ׺
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,8) . '-';
    $uuid .= substr($str,8,4) . '-';
    $uuid .= substr($str,12,4) . '-';
    $uuid .= substr($str,16,4) . '-';
    $uuid .= substr($str,20,12);
    return $prefix . $uuid;
}
/**
 * ��ȡ�ļ�����չ��
 * @param $file �ļ���ȫ·��
 */
function get_extension($file)
{
    $info = pathinfo($file);
    return $info['extension'];
}

/*
 * ����:��ʽ��������߶�������ݵ����
 * Author : WJH
 * Date:2014-11-20
 * @param $data string or array or float or int or object
 * return null
 */
function pr($data)
{
    echo '<pre>';
    if(is_array($data)){
        print_r($data);
    }elseif(is_object($data)){
        print_r($data);
    }else{
        echo $data;
    }
    echo '</pre>';
}
/**
 * ����PHP�������ͱ�������Ψһ��ʶ��
 * @param mixed $mix ����
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * XML����
 * @param mixed $data ����
 * @param string $root ���ڵ���
 * @param string $item �����������ӽڵ���
 * @param string $attr ���ڵ�����
 * @param string $id   ���������ӽڵ�keyת����������
 * @param string $encoding ���ݱ���
 * @return string
 */
function xml_encode($data, $root='think', $item='item', $attr='', $id='id', $encoding='utf-8') {
    if(is_array($attr)){
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr   = trim($attr);
    $attr   = empty($attr) ? '' : " {$attr}";
    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml   .= "<{$root}{$attr}>";
    $xml   .= data_to_xml($data, $item, $id);
    $xml   .= "</{$root}>";
    return $xml;
}

/**
 * ����XML����
 * @param mixed  $data ����
 * @param string $item ��������ʱ�Ľڵ�����
 * @param string $id   ��������keyת��Ϊ��������
 * @return string
 */
function data_to_xml($data, $item='item', $id='id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if(is_numeric($key)){
            $id && $attr = " {$id}=\"{$key}\"";
            $key  = $item;
        }
        $xml    .=  "<{$key}{$attr}>";
        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml    .=  "</{$key}>";
    }
    return $xml;
}

/**
 * session������
 * @param string|array $name session���� ���Ϊ�������ʾ����session����
 * @param mixed $value sessionֵ
 * @return mixed
 */
function session($name,$value='') {
    $prefix   =  C('SESSION_PREFIX');
    if(is_array($name)) { // session��ʼ�� ��session_start ֮ǰ����
        if(isset($name['prefix'])) C('SESSION_PREFIX',$name['prefix']);
        if(C('VAR_SESSION_ID') && isset($_REQUEST[C('VAR_SESSION_ID')])){
            session_id($_REQUEST[C('VAR_SESSION_ID')]);
        }elseif(isset($name['id'])) {
            session_id($name['id']);
        }
        ini_set('session.auto_start', 0);
        if(isset($name['name']))            session_name($name['name']);
        if(isset($name['path']))            session_save_path($name['path']);
        if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
        if(isset($name['expire']))          ini_set('session.gc_maxlifetime', $name['expire']);
        if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
        if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
        if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
        if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
        if(isset($name['type']))            C('SESSION_TYPE',$name['type']);
        if(C('SESSION_TYPE')) { // ��ȡsession����
            $class      = 'Session'. ucwords(strtolower(C('SESSION_TYPE')));
            // ���������
            if(require_cache(EXTEND_PATH.'Driver/Session/'.$class.'.class.php')) {
                $hander = new $class();
                $hander->execute();
            }else {
                // ��û�ж���
                throw_exception(L('_CLASS_NOT_EXIST_').': ' . $class);
            }
        }
        // ����session
        if(C('SESSION_AUTO_START'))  session_start();
    }elseif('' === $value){ 
        if(0===strpos($name,'[')) { // session ����
            if('[pause]'==$name){ // ��ͣsession
                session_write_close();
            }elseif('[start]'==$name){ // ����session
                session_start();
            }elseif('[destroy]'==$name){ // ����session
                $_SESSION =  array();
                session_unset();
                session_destroy();
            }elseif('[regenerate]'==$name){ // ��������id
                session_regenerate_id();
            }
        }elseif(0===strpos($name,'?')){ // ���session
            $name   =  substr($name,1);
            if(strpos($name,'.')){ // ֧������
                list($name1,$name2) =   explode('.',$name);
                return $prefix?isset($_SESSION[$prefix][$name1][$name2]):isset($_SESSION[$name1][$name2]);
            }else{
                return $prefix?isset($_SESSION[$prefix][$name]):isset($_SESSION[$name]);
            }
        }elseif(is_null($name)){ // ���session
            if($prefix) {
                unset($_SESSION[$prefix]);
            }else{
                $_SESSION = array();
            }
        }elseif($prefix){ // ��ȡsession
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;  
            }else{
                return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;                
            }            
        }else{
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;  
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:null;
            }            
        }
    }elseif(is_null($value)){ // ɾ��session
        if($prefix){
            unset($_SESSION[$prefix][$name]);
        }else{
            unset($_SESSION[$name]);
        }
    }else{ // ����session
        if($prefix){
            if (!is_array($_SESSION[$prefix])) {
                $_SESSION[$prefix] = array();
            }
            $_SESSION[$prefix][$name]   =  $value;
        }else{
            $_SESSION[$name]  =  $value;
        }
    }
}

/**
 * Cookie ���á���ȡ��ɾ��
 * @param string $name cookie����
 * @param mixed $value cookieֵ
 * @param mixed $options cookie����
 * @return mixed
 */
function cookie($name, $value='', $option=null) {
    // Ĭ������
    $config = array(
        'prefix'    =>  C('COOKIE_PREFIX'), // cookie ����ǰ׺
        'expire'    =>  C('COOKIE_EXPIRE'), // cookie ����ʱ��
        'path'      =>  C('COOKIE_PATH'), // cookie ����·��
        'domain'    =>  C('COOKIE_DOMAIN'), // cookie ��Ч����
    );
    // ��������(�Ḳ���a������)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config     = array_merge($config, array_change_key_case($option));
    }
    // ���ָ��ǰ׺������cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // Ҫɾ����cookieǰ׺����ָ����ɾ��config���õ�ָ��ǰ׺
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// ���ǰ׺Ϊ���ַ�������������ֱ�ӷ���
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        if(isset($_COOKIE[$name])){
            $value =    $_COOKIE[$name];
            if(0===strpos($value,'think:')){
                $value  =   substr($value,6);
                return array_map('urldecode',json_decode(MAGIC_QUOTES_GPC?stripslashes($value):$value,true));
            }else{
                return $value;
            }
        }else{
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // ɾ��ָ��cookie
        } else {
            // ����cookie
            if(is_array($value)){
                $value  = 'think:'.json_encode(array_map('urlencode',$value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

/**
 * ���ض�̬��չ�ļ�
 * @return void
 */
function load_ext_file() {
    // �����Զ����ⲿ�ļ�
    if(C('LOAD_EXT_FILE')) {
        $files      =  explode(',',C('LOAD_EXT_FILE'));
        foreach ($files as $file){
            $file   = COMMON_PATH.$file.'.php';
            if(is_file($file)) include $file;
        }
    }
    // �����Զ���Ķ�̬�����ļ�
    if(C('LOAD_EXT_CONFIG')) {
        $configs    =  C('LOAD_EXT_CONFIG');
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $key=>$config){
            $file   = CONF_PATH.$config.'.php';
            if(is_file($file)) {
                is_numeric($key)?C(include $file):C($key,include $file);
            }
        }
    }
}

/**
 * ��ȡ�ͻ���IP��ַ
 * @param integer $type �������� 0 ����IP��ַ 1 ����IPV4��ַ����
 * @return mixed
 */
function get_client_ip($type = 0) {
	$type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP��ַ�Ϸ���֤
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * ����HTTP״̬
 * @param integer $code ״̬��
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // ȷ��FastCGIģʽ������
        header('Status:'.$code.' '.$_status[$code]);
    }
}

// ���˱��еı��ʽ
function filter_exp(&$value){
    if (in_array(strtolower($value),array('exp','or'))){
        $value .= ' ';
    }
}
