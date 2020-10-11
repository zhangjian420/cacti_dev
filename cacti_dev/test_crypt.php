<?php

set_include_path('./include/vendor/phpseclib/');
include('Crypt/Base.php');
include('Math/BigInteger.php');
include('Crypt/Hash.php');
include('Crypt/RSA.php');
include('Crypt/Rijndael.php');
include('Crypt/AES.php');
include('Crypt/Random.php');

// 加密lic文件
function encrypt($content, $rsa_key){
    $rsa = new \phpseclib\Crypt\RSA();
    $aes = new \phpseclib\Crypt\Rijndael();
    $aes_key = \phpseclib\Crypt\Random::string(192);
    
    $aes->setKey($aes_key);
    $ciphertext = base64_encode($aes->encrypt($content));
    $rsa->loadKey($rsa_key);
    $aes_key = base64_encode($rsa->encrypt($aes_key));
    $aes_key_length = str_pad(dechex(strlen($aes_key)),3,'0',STR_PAD_LEFT);
    
    $file_content = $aes_key_length . $aes_key . $ciphertext;
    $handle = fopen("./itms.lic", "w") or die ("不能写入文件");
    fwrite($handle, $file_content);
    fclose($handle);
    
    echo "aa";
    echo "\r\n";
    echo $file_content;
    echo "\r\n";
}

// 解密lic文件
function decrypt(){
    $handle = fopen("./itms.lic", "r") or die ("不能读取文件");
    $content = fread($handle, filesize("./itms.lic"));
    fclose($handle);
    
    //$content = "200mQPuEYcjZRYY+pMQe63FprPTzcifWgsan4WLcgQM1bVjfRqaNd5vhUfzxUeB6Lnd8yRBjnx2JS5XBG8W6w/rWz41e+GBN1RwnuWSVmDjJTaLUUBXDVAZg/5AO0Pnxh+AkaFYTrtjnRixJwff7sEdQllQsRpXsvHKXsx7OwiEq+omQ3d9vZyEPYJ+olOh7ICp4ZDO8Z5m5ZtakJBnvisnAFlgg7vtWUnJETtM0/aYLBvWItwKiJ6qejL31HufQrcN0XvW532gRNko1FYLnriCuohLXuAswDNNZI5reT2/XFkiKQzvyXLwSShp7PgshBLF0OTJsWvODbUIjiEOpRHK4RXpFAcsA9YWv0QEQLi0OOLl5wv4QVUuk9y8AM1Io/9ZG3HvkEyI7vJFLHnmfnuFlI6PwBBaGBlLZ3SjhCFE5lvFpExkzowqQxRdAHejGxXXRgjheFQn9nNgAxdpgpaQwDagE+RxEOvCyhB2zW5W9yWLyw8OGk1Hh+lq1thpNpM1ba07KbC5XGtOBkRhs1BI4WOVglW8sHjnRiqoLzMcvn/cKGflAHP7laTn5PYNSnW4";
    
    echo "bb";
    echo "\r\n";
    echo $content;
    echo "\r\n";
    
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
    
    $arr = explode(PHP_EOL,$plaintext);
    print_r($arr);
    
    return $arr;
    
}

$machine = get_machine_code();
echo "machine = " . $machine . "\r\n";

encrypt("3
20
5
2021-01-01
1
1
1
1
1
云广互联
b8:2a:72:d1:f8:e5
172.16.22.103,172.16.9.17
$machine","-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALRL81VvBeVcMs+E
mf+OgNI66m2xDYaSlLMxNWKcE3cJjMLwoQo0dUmEzp2K370WkPuV49TyNdCo97th
17k0Uo+qFL7fYXDczq+IfvqWfzAtl4vZfhNm//aL+rkLPVxdci++yOWlZzqPanSC
Ar0Q/GPHA+6dnYhSwJ6Q/XPyPC8BAgMBAAECgYBJ8AuuMYV9db3wlDSDNPFnRXn1
2fHuChapFbkK426oFmZ/WybvhGvE5o3E1brDVInIYsO4BExFccWGNq286dQhHizs
ByMcBDrDkQBLfO0yAGnxRJEIey1jPBHKTrw2mH/ThNDFnyELySozz7FI6YFmSVq6
+YkT14nz6jFTKOeb8QJBANeR1I+foH+C57yZRe4jOhjFEq7rCxoXhP3fBIJ0Wfsk
xYT8+zcH3qKw+cIUrUHhIChjpyGDgXWEC8+JWbdH0B8CQQDWHI4lF6APDGHquOT1
PYdiA9/JR+MwKX84LlBcra2kCp+2nML/ZbG09sZJKLF7kTn98eNkIr0iEV896tmg
xZzfAkBnHMZD/OLUm1UljVs50XfUqU+Kg7tHu8BNfwO1Mtpnmusv9aJkbEs+HtEY
2LMXNAwhxakICtM91u+fUd+sH5mZAkEAjFDYYryR22rM/KtA+Orivzw0u08ONzDq
u6G2bpYvVnLT6jPfoso9ZI/YsHcnoQgyjhaoY6ZUdnAWD5jKGI+I1wJAYXFkKxte
IpPIWzHjnKKS5uQ44aA98TRJBnQ48qMl4qKWFQTyMz7TzgJDMUw04MgWtEWC1ZW0
qTUFLpF3HNJS5g==
-----END PRIVATE KEY-----");

echo "\r\n";

decrypt();

$mac = new GetMacAddr(PHP_OS);
echo $mac->mac_addr; 
echo "\r\n";

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

function get_machine_code(){
    $cpu = shell_exec("dmidecode -t 4 | grep ID | head -n 1");
    if(!empty($cpu)){
        $cpu = trim($cpu);
    }
    $system = shell_exec("dmidecode -s system-serial-number");
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