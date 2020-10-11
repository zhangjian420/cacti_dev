<?php

set_include_path('./include/vendor/phpseclib/');
include('Crypt/Base.php');
include('Math/BigInteger.php');
include('Crypt/Hash.php');
include('Crypt/RSA.php');
include('Crypt/Rijndael.php');
include('Crypt/AES.php');
include('Crypt/Random.php');

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
    
    $content = md5($cpu . "    a    " . $system . "    b       " . $disk);
    
    $rsa_key = "-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIlaNgUBTA4e/gPIgxCPRP2xbAnbKUe+
AKNq4FE+hU6Zbzc9HoYTOPBBnWjhE2bM1KsO/R2Pun4Zb0WKhqd3WWUCAwEAAQ==
-----END PUBLIC KEY-----";
    
    $rsa = new \phpseclib\Crypt\RSA();
    $aes = new \phpseclib\Crypt\Rijndael();
    $aes_key = \phpseclib\Crypt\Random::string(192);
    
    $aes->setKey($aes_key);
    $ciphertext = base64_encode($aes->encrypt($content));
    $rsa->loadKey($rsa_key);
    $aes_key = base64_encode($rsa->encrypt($aes_key));
    $aes_key_length = str_pad(dechex(strlen($aes_key)),3,'0',STR_PAD_LEFT);
    
    $file_content = $aes_key_length . $aes_key . $ciphertext;
    
    return $file_content;
}

echo trim(get_machine_code()) . "\r\n";