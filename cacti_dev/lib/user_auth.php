<?php 

function checklic($user){
    $lic_msg = decrypt();
    if (is_array($lic_msg)) {
        $ret_msg = "";
        if (empty($ret_msg)) { // 截止时间
            if (sizeof($lic_msg) < 4) {
                $ret_msg = "授权文件错误，code:3";
            }else if(!empty(trim($lic_msg[3]))){
                $_SESSION['sess_time_lic'] = trim($lic_msg[3]);
                if(strtotime($lic_msg[3]) < time()){
                    $ret_msg = "授权已过期";
                }
            }
        }
        if(empty($ret_msg)/*  && $user["id"] != 1 */){ // 限制主机个数，但是超管可以登录，方便删除主机
            if (sizeof($lic_msg) < 1) {
                $ret_msg = "授权文件错误，code:0";
            }else if(!empty(trim($lic_msg[0]))){
                $host_num = db_fetch_cell("select count(*) from host");
                $_SESSION['sess_host_num_lic'] = trim($lic_msg[0]);
                if (isset($host_num) && $host_num > trim($lic_msg[0])) {
                    $ret_msg = "主机数量大于授权数量，请重新授权";
                }
            }
        }
        if(empty($ret_msg)/*  && $user["id"] != 1 */){ // 限制图形个数，但是超管可以登录，方便删除图形
            if (sizeof($lic_msg) < 2) {
                $ret_msg = "授权文件错误，code:1";
            }else if(!empty(trim($lic_msg[1]))){
                $graph_num = db_fetch_cell("select count(*) from graph_local where host_id > 0");
                $_SESSION['sess_graph_num_lic'] = trim($lic_msg[1]);
                if (isset($graph_num) && $graph_num > trim($lic_msg[1])) {
                    $ret_msg = "图形数量大于授权数量，请重新授权";
                }
            }
        }
        if(empty($ret_msg)/*  && $user["id"] != 1 */){ // 限制用户个数，但是超管可以登录，方便删除用户
            if (sizeof($lic_msg) < 3) {
                $ret_msg = "授权文件错误，code:2";
            }else if(!empty(trim($lic_msg[2]))){
                $user_num = db_fetch_cell("select count(*) from user_auth");
                $_SESSION['sess_user_num_lic'] = trim($lic_msg[2]);
                if (isset($user_num) && $user_num > trim($lic_msg[2])) {
                    $ret_msg = "用户数量大于授权数量，请重新授权";
                }
            }
        }
        if(empty($ret_msg)){ // 限制企业，如果是admin进去的话，可以修改企业名字
            if (sizeof($lic_msg) < 10) {
                $ret_msg = "授权文件错误，code:9";
            }else if(!empty(trim($lic_msg[9]))){
                $_SESSION['sess_company_name_lic'] = trim($lic_msg[9]);
                if($user["id"] != 1){
                    $company_name = db_fetch_cell("select value from settings where name = 'company_name'");
                    if (empty($company_name) || $company_name != trim($lic_msg[9])) {
                        $ret_msg = "授权文件错误，code:9";
                    }
                }
            }
        }
        if(empty($ret_msg)){ // 限制MAC
            if (sizeof($lic_msg) < 11) {
                $ret_msg = "授权文件错误，code:10";
            }else if(!empty(trim($lic_msg[10]))){
                $mac = new GetMacAddr(PHP_OS);
                $mac_addr = $mac->mac_addr;
                //cacti_log("mac = " . $mac_addr);
                $_SESSION['sess_mac_lic'] = trim($lic_msg[10]);
                if (trim($mac_addr) != trim($lic_msg[10])) {
                    $ret_msg = "授权文件错误，code:10";
                }
            }
        }
        if(empty($ret_msg)){ // 限制IP
            if (sizeof($lic_msg) < 12) {
                $ret_msg = "授权文件错误，code:11";
            }else if(!empty(trim($lic_msg[11]))){
                $ip = get_localip();
                //cacti_log("local ip = " . $ip);
                $_SESSION['sess_ip_lic'] = trim($lic_msg[11]);
                if (strpos(trim($lic_msg[11]), trim($ip)) !== false) {
                }else{
                    $ret_msg = "授权文件错误，code:11";
                }
            }
        }
        if(empty($ret_msg)){ // 限制机器码
            if (sizeof($lic_msg) < 13) {
                $ret_msg = "授权文件错误，code:12";
            }else if(!empty(trim($lic_msg[12]))){
                $machine = get_machine_code();
                cacti_log("machine code = " . trim($lic_msg[12]) . "     " . $machine);
                $_SESSION['sess_machine_lic'] = trim($lic_msg[12]);
                if (trim($machine) != trim($lic_msg[12])) {
                    $ret_msg = "授权文件错误，code:12";
                }
            }
        }
        //cacti_log("report = " . $lic_msg[4] . " assets = " . $lic_msg[5] . " overview = " . $lic_msg[6] . " qunee = " . $lic_msg[7] . " rep = " . $lic_msg[8]);
        if(empty($ret_msg) && empty(trim($lic_msg[4]))){ // 限制报表模块
            api_plugin_uninstall("report");
            $_SESSION['sess_report_status'] = "off";
        }else{
            $_SESSION['sess_report_status'] = "on";
        }
        if(empty($ret_msg) && empty(trim($lic_msg[5]))){ // 限制资产管理模块
            api_plugin_uninstall("assets");
            $_SESSION['sess_assets_status'] = "off";
        }else{
            $_SESSION['sess_assets_status'] = "on";
        }
        if(empty($ret_msg) && empty(trim($lic_msg[6]))){ // 限制监控大屏模块
            api_plugin_uninstall("overview");
            $_SESSION['sess_overview_status'] = "off";
        }else{
            $_SESSION['sess_overview_status'] = "on";
        }
        if(empty($ret_msg) && empty(trim($lic_msg[7]))){ // 限制拓扑图模块
            api_plugin_uninstall("qunee");
            $_SESSION['sess_qunee_status'] = "off";
        }else{
            $_SESSION['sess_qunee_status'] = "on";
        }
        if(empty($ret_msg) && empty(trim($lic_msg[8]))){ // 限制报告模块
            $_SESSION['sess_reports_status'] = "off";
        }else{
            $_SESSION['sess_reports_status'] = "on";
        }
        
        // 如果有错误，提示错误
        if (!empty($ret_msg)) {
            $lic_msg = $ret_msg;
        }
    }
    
    if (!empty($lic_msg) && !is_array($lic_msg)) {
        display_custom_error_message($lic_msg);
        header('Location: index.php');
        exit;
    }
}

// 解密lic文件
function decrypt(){
    global $config;
    set_include_path($config['include_path'] . '/vendor/phpseclib/');
    include_once('Math/BigInteger.php');
    include_once('Crypt/Base.php');
    include_once('Crypt/Hash.php');
    include_once('Crypt/Random.php');
    include_once('Crypt/RSA.php');
    include_once('Crypt/Rijndael.php');
    
    if (!file_exists("./itms.lic")) {
        return "系统没有授权";
    }
    $handle = fopen("./itms.lic", "r") or die ("不能读取文件");
    $content = fread($handle, filesize("./itms.lic"));
    fclose($handle);
    
    $rsa = new \phpseclib\Crypt\RSA();
    $aes = new \phpseclib\Crypt\Rijndael();
    $rsa_decrypt_key = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC0S/NVbwXlXDLPhJn/joDSOupt
sQ2GkpSzMTVinBN3CYzC8KEKNHVJhM6dit+9FpD7lePU8jXQqPe7Yde5NFKPqhS+
32Fw3M6viH76ln8wLZeL2X4TZv/2i/q5Cz1cXXIvvsjlpWc6j2p0ggK9EPxjxwPu
nZ2IUsCekP1z8jwvAQIDAQAB
-----END PUBLIC KEY-----";
    
    $aes_key_length = hexdec(substr($content,0,3));
    $aes_key = base64_decode(substr($content,3,$aes_key_length));
    $ciphertext = base64_decode(substr($content,3+$aes_key_length));
    
    $rsa->loadKey($rsa_decrypt_key);
    $aes_key = $rsa->decrypt($aes_key);
    $aes->setKey($aes_key);
    $plaintext = $aes->decrypt($ciphertext);
    
    if (empty($plaintext)) {
        return "系统没有授权";
    }
    $arr = explode(PHP_EOL,$plaintext);
    if (sizeof($arr) == 0) {
        return "授权文件错误";
    }
    //array_pop($arr);
    return $arr;
    
}

// 解密机器码
function decrypt_machine_code($content){
    $rsa = new \phpseclib\Crypt\RSA();
    $aes = new \phpseclib\Crypt\Rijndael();
    $rsa_decrypt_key = "-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBAIlaNgUBTA4e/gPIgxCPRP2xbAnbKUe+AKNq4FE+hU6Zbzc9HoYT
OPBBnWjhE2bM1KsO/R2Pun4Zb0WKhqd3WWUCAwEAAQJASNKej3rHkzkVXnYiH1aG
sqct6+/Z7CKt/fa9ZfXrAeeFYhOdV/DOwb7CdgpUKpnRbLc9A9Zg7Ee/kAO6RyQ3
QQIhAO2cgq3Npd5KF6oj8qnoO2bWGwYkAf8j+42QYIhDjkx1AiEAk/tl19Ubnu1p
XHbqDInkYUfu70hU9ZZsJ9Vso2q0+zECIQCxoRwsFB2YlOkl/yOskvi9QvgG2ipH
8z1dsf4IQACD9QIgbRqaQOJHZgqGdvzZnPsBndPKTzNWKGeKQGgzm9ytqNECIGBm
rCYWC1LQk85Aab7ju8Fk5jqRFahPVIwGUmJL60Og
-----END RSA PRIVATE KEY-----";
    
    $aes_key_length = hexdec(substr($content,0,3));
    $aes_key = base64_decode(substr($content,3,$aes_key_length));
    $ciphertext = base64_decode(substr($content,3+$aes_key_length));
    
    $rsa->loadKey($rsa_decrypt_key);
    $aes_key = $rsa->decrypt($aes_key);
    $aes->setKey($aes_key);
    $plaintext = $aes->decrypt($ciphertext);
    return trim($plaintext);
}

function get_localip(){
    $ip = shell_exec("/sbin/ip addr show | awk '/inet / {print $2}' |grep -v 127.0.0.1 |cut -d'/' -f1 | head -1");
    if (empty($ip)) {
        return "127.0.0.1";
    }
    return $ip;
}

function get_machine_code(){
    $cpu = shell_exec("sudo dmidecode -t 4 | grep ID | head -n 1");
    if(!empty($cpu)){
        $cpu = trim($cpu);
    }
    $system = shell_exec("sudo dmidecode -s system-serial-number");
    if(!empty($system)){
        $system = trim($system);
    }
    $disk = "";
    $dh = opendir('/dev/disk/by-uuid/');
    while($file = readdir($dh)){
        if(is_link('/dev/disk/by-uuid/'.$file)){
            if( realpath('/dev/disk/by-uuid/'.$file) == "/dev/sda1"){
                $disk = $file;
            }
        }
    }
    if(!empty($disk)){
        $disk = trim($disk);
    }
    
    return md5($cpu . "    a    " . $system . "    b       " . $disk);
}

class GetMacAddr{
    var $return_array = array(); // 返回带有MAC地址的字串数组
    var $mac_addr;
    
    function __construct($os_type){
        switch ( strtolower($os_type) ){
            case "linux":
                $this->forLinux();
                break;
            case "solaris":
                break;
            case "unix":
                break;
            case "aix":
                break;
            default:
                $this->forWindows();
                break;
                
        }
        
        
        $temp_array = array();
        foreach ( $this->return_array as $value ){
            
            if (
                preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i",$value,
                    $temp_array ) ){
                        $this->mac_addr = $temp_array[0];
                        break;
            }
            
        }
        unset($temp_array);
        return $this->mac_addr;
    }
    
    
    function forWindows(){
        @exec("ipconfig /all", $this->return_array);
        if ( $this->return_array )
            return $this->return_array;
            else{
                $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
                if ( is_file($ipconfig) )
                    @exec($ipconfig." /all", $this->return_array);
                    else
                        @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array);
                        return $this->return_array;
            }
    }
    
    
    
    function forLinux(){
        @exec("ifconfig -a", $this->return_array);
        return $this->return_array;
    }
    
} 
?>