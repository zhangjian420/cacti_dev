<?php

include('./include/auth.php');

/* file: system_authorize.php, action: edit */
$fields_system_authorize_edit = array(
    'company_name' => array(
        'method' => 'textbox',
        'friendly_name' => "授权企业名称",
        'placeholder' => "授权企业名称，必须和授权企业系统中名称一致",
        'description' => "授权企业名称，必须和授权企业系统中名称一致",
        'max_length' => '200',
        'size' => '50',
        'value' => ''
    ),
    'host_num' => array(
        'method' => 'textbox',
        'friendly_name' => "设备数量",
        'placeholder' => "系统授权设备数量，0不限制",
        'description' => "系统授权设备数量，0不限制",
        'value' => '0',
        'size' => '50',
        'max_length' => '200'
    ),
    'graph_num' => array(
        'method' => 'textbox',
        'friendly_name' => "图形数量",
        'placeholder' => "系统授权图形数量，0不限制",
        'description' => "系统授权图形数量，0不限制",
        'value' => '0',
        'size' => '50',
        'max_length' => '200'
    ),
    'user_num' => array(
        'method' => 'textbox',
        'friendly_name' => "用户数量",
        'placeholder' => "系统授权用户数量，0不限制",
        'description' => "系统授权用户数量，0不限制",
        'value' => '0',
        'size' => '50',
        'max_length' => '200'
    ),
    'lic_date' => array(
        'method' => 'textbox',
        'friendly_name' => "到期时间",
        'placeholder' => "系统授权到期时间，0不限制，格式为2020-01-01",
        'description' => "系统授权到期时间，0不限制，格式为2020-01-01",
        'value' => '0',
        'size' => '50',
        'max_length' => '200'
    ),
    'mac' => array(
        'method' => 'textbox',
        'friendly_name' => "设备MAC地址",
        'placeholder' => "设备使用网卡的MAC地址，格式为b8:2a:72:d1:f8:e5",
        'description' => "设备使用网卡的MAC地址，格式为b8:2a:72:d1:f8:e5",
        'value' => '',
        'size' => '50',
        'max_length' => '200'
    ),
    'ip' => array(
        'method' => 'textbox',
        'friendly_name' => "设备IP地址，多个地址以英文逗号分隔",
        'placeholder' => "设备使用网卡的IP地址，格式为192.168.1.1,192.168.1.2",
        'description' => "设备使用网卡的IP地址，格式为192.168.1.1,192.168.1.2",
        'value' => '',
        'size' => '50',
        'max_length' => '200'
    ),
    'machine_code' => array(
        'method' => 'textarea',
        'textarea_rows' => '3',
        'textarea_cols' => '60',
        'friendly_name' => "客户机器码",
        'placeholder' => "请输入客户机上运行run_machine_code.php得到的机器码",
        'description' => "请输入客户机上运行run_machine_code.php得到的机器码",
        'value' => ''
    ),
    'enable_report' => array(
        'friendly_name' => "允许报表模块",
        'method' => 'drop_array',
        'value' => 1,
        'array' => array (
            1 => __('是'),
            0 => __('否'))
    ),
    'enable_assets' => array(
        'friendly_name' => "允许资产管理模块",
        'method' => 'drop_array',
        'value' => 1,
        'array' => array (
            1 => __('是'),
            0 => __('否'))
    ),
    'enable_overview' => array(
        'friendly_name' => "允许监控大屏模块",
        'method' => 'drop_array',
        'value' => 1,
        'array' => array (
            1 => __('是'),
            0 => __('否'))
    ),
    'enable_topo' => array(
        'friendly_name' => "允许拓扑图模块",
        'method' => 'drop_array',
        'value' => 1,
        'array' => array (
            1 => __('是'),
            0 => __('否'))
    ),
    'enable_reports' => array(
        'friendly_name' => "允许报告模块",
        'method' => 'drop_array',
        'value' => 1,
        'array' => array (
            1 => __('是'),
            0 => __('否'))
    ),
    'id' => array(
        'method' => 'hidden_zero',
        'value' => '|arg1:id|'
    ),
    'save_component_site' => array(
        'method' => 'hidden',
        'value' => '1'
    )
);

set_default_action();

switch(get_nfilter_request_var('action')) {
    case 'save':
        system_authorize_save();
        break;
	default:
	    top_header();
	    system_authorize_edit();
	    bottom_footer();
	    break;
}

/**
 * 进入编辑页面
 */
function system_authorize_edit(){
    global $fields_system_authorize_edit;
    
    /* ================= input validation ================= */
    get_filter_request_var('id');
    /* ==================================================== */
    if (!isempty_request_var('id')) {
        //$system_authorize = db_fetch_row_prepared('SELECT * FROM system_authorize WHERE id = ?', array(get_request_var('id')));
    }
    form_start('system_authorize.php', 'system_authorize',true);
    html_start_box("生成Lic", '100%', true, '3', 'center', '');
    draw_edit_form(
        array(
            'config' => array('no_form_tag' => true),
            'fields' => inject_form_variables($fields_system_authorize_edit,array())
        )
    );
    
    html_end_box(true, true);
    
    form_save_button('system_authorize.php', 'return',"id",false);
    ?>
	<script type='text/javascript'>

	$(function() {
		$("#lic_date").prop("readonly", true).datepicker({
            changeYear: true,
            changeMonth: true,
            dateFormat: "yy-mm-dd"
	    });
	});
	</script>
	<?php
}

/**
 * 点击修改或者添加按钮
 */
function system_authorize_save(){
    
    if (isset_request_var('save_component_site')) {
        form_input_validate(get_nfilter_request_var('company_name'), 'company_name', '', false, 3);
        form_input_validate(get_nfilter_request_var('host_num'), 'host_num', '^[0-9]+$', false, 3);
        form_input_validate(get_nfilter_request_var('graph_num'), 'graph_num', '^[0-9]+$', false, 3);
        form_input_validate(get_nfilter_request_var('host_num'), 'user_num', '^[0-9]+$', false, 3);
        form_input_validate(get_nfilter_request_var('lic_date'), 'lic_date', '', false, 3);
        form_input_validate(get_nfilter_request_var('mac'), 'mac', '', false, 3);
        form_input_validate(get_nfilter_request_var('ip'), 'ip', '', false, 3);
        form_input_validate(get_nfilter_request_var('machine_code'), 'machine_code', '', false, 3);
        
        if (is_error_message()) {
            header("Location: system_authorize.php");
            exit;
        }
        
        $machine_code = get_request_var("machine_code");
        
        $content = get_request_var('host_num') . "\n" . get_request_var('graph_num') 
        . "\n" . get_request_var('user_num') . "\n" . get_request_var('lic_date') 
        . "\n" . get_request_var('enable_report') . "\n" . get_request_var('enable_assets') 
        . "\n" . get_request_var('enable_overview') . "\n" . get_request_var('enable_topo') 
        . "\n" . get_request_var('enable_reports'). "\n" . get_request_var('company_name')
        . "\n" . get_request_var('mac'). "\n" . get_request_var('ip') . "\n" . decrypt_machine_code($machine_code);
        
        global $config;
        set_include_path($config['include_path'] . '/vendor/phpseclib/');
        include_once('Math/BigInteger.php');
        include_once('Crypt/Base.php');
        include_once('Crypt/Hash.php');
        include_once('Crypt/Random.php');
        include_once('Crypt/RSA.php');
        include_once('Crypt/Rijndael.php');
        
        $rsa = new \phpseclib\Crypt\RSA();
        $aes = new \phpseclib\Crypt\Rijndael();
        $aes_key = \phpseclib\Crypt\Random::string(192);
        
        $aes->setKey($aes_key);
        $ciphertext = base64_encode($aes->encrypt($content));
        $rsa->loadKey("-----BEGIN PRIVATE KEY-----
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
        $aes_key = base64_encode($rsa->encrypt($aes_key));
        $aes_key_length = str_pad(dechex(strlen($aes_key)),3,'0',STR_PAD_LEFT);
        
        $file_content = $aes_key_length . $aes_key . $ciphertext;
        $handle = fopen("./user.lic", "w+") or die ("不能写入文件");
        fwrite($handle, $file_content);
        fclose($handle);
        
        header("Content-type: application/txt" );
        header('Content-Length: ' . filesize("./user.lic")); //下载文件大小
        Header ("Content-Disposition: attachment; filename=itms.lic");
        echo file_get_contents("./user.lic");
        
        unlink("./user.lic");
    }
}

// 解密machine_code
function decrypt_machine_code($content){
    global $config;
    set_include_path($config['include_path'] . '/vendor/phpseclib/');
    include_once('Math/BigInteger.php');
    include_once('Crypt/Base.php');
    include_once('Crypt/Hash.php');
    include_once('Crypt/Random.php');
    include_once('Crypt/RSA.php');
    include_once('Crypt/Rijndael.php');
    
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
