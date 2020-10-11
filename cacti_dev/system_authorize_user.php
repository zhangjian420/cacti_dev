<?php

include('./include/auth.php');

set_default_action();

switch(get_nfilter_request_var('action')) {
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
    $fields_system_authorize_user_edit = array(
        'company_name' => array(
            'method' => 'other',
            'friendly_name' => "授权企业名称",
            'description' => "授权企业名称，必须和授权企业系统中名称一致",
            'value' => isset($_SESSION['sess_company_name_lic']) ? $_SESSION['sess_company_name_lic'] : ""
        ),
        'host_num' => array(
            'method' => 'other',
            'friendly_name' => "设备数量",
            'description' => "系统授权设备数量，0不限制",
            'value' => isset($_SESSION['sess_host_num_lic']) ? $_SESSION['sess_host_num_lic'] : ""
        ),
        'graph_num' => array(
            'method' => 'other',
            'friendly_name' => "图形数量",
            'description' => "系统授权图形数量，0不限制",
            'value' => isset($_SESSION['sess_graph_num_lic']) ? $_SESSION['sess_graph_num_lic'] : ""
        ),
        'user_num' => array(
            'method' => 'other',
            'friendly_name' => "用户数量",
            'description' => "系统授权用户数量，0不限制",
            'value' => isset($_SESSION['sess_user_num_lic']) ? $_SESSION['sess_user_num_lic'] : ""
        ),
        'lic_date' => array(
            'method' => 'other',
            'friendly_name' => "到期时间",
            'description' => "系统授权到期时间，0不限制，格式为2020-01-01",
            'value' => isset($_SESSION['sess_time_lic']) ? $_SESSION['sess_time_lic'] : ""
        ),
        'mac' => array(
            'method' => 'other',
            'friendly_name' => "设备MAC地址",
            'description' => "设备使用网卡的MAC地址，格式为b8:2a:72:d1:f8:e5",
            'value' => isset($_SESSION['sess_mac_lic']) ? $_SESSION['sess_mac_lic'] : ""
        ),
        'ip' => array(
            'method' => 'other',
            'friendly_name' => "设备IP地址，多个地址以英文逗号分隔",
            'description' => "设备使用网卡的IP地址，格式为192.168.1.1,192.168.1.2",
            'value' => isset($_SESSION['sess_ip_lic']) ? $_SESSION['sess_ip_lic'] : ""
        ),
        'enable_report' => array(
            'friendly_name' => "允许报表模块",
            'method' => 'other',
            'value' => (isset($_SESSION['sess_report_status']) && $_SESSION['sess_report_status'] == "off") ? "不允许" : "允许",
        ),
        'enable_assets' => array(
            'friendly_name' => "允许资产管理模块",
            'method' => 'other',
            'value' => (isset($_SESSION['sess_assets_status']) && $_SESSION['sess_assets_status'] == "off") ? "不允许" : "允许",
        ),
        'enable_overview' => array(
            'friendly_name' => "允许监控大屏模块",
            'method' => 'other',
            'value' => (isset($_SESSION['sess_overview_status']) && $_SESSION['sess_overview_status'] == "off") ? "不允许" : "允许",
        ),
        'enable_topo' => array(
            'friendly_name' => "允许拓扑图模块",
            'method' => 'other',
            'value' => (isset($_SESSION['sess_topo_status']) && $_SESSION['sess_topo_status'] == "off") ? "不允许" : "允许",
        ),
        'enable_reports' => array(
            'friendly_name' => "允许报告模块",
            'method' => 'other',
            'value' => (isset($_SESSION['sess_reports_status']) && $_SESSION['sess_reports_status'] == "off") ? "不允许" : "允许",
        )
    );
    
    form_start('system_authorize_user.php', 'system_authorize',true);
    html_start_box("查看授权", '100%', true, '3', 'center', '');
    draw_edit_form(
        array(
            'config' => array('no_form_tag' => true),
            'fields' => inject_form_variables($fields_system_authorize_user_edit,array())
        )
    );
    html_end_box(true, true);
}

