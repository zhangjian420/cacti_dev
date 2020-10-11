<?php

function import_csv_hosts($csv_arr){
    foreach ($csv_arr as $value){
        api_device_save(
            get_nfilter_request_var('id'), 
            get_nfilter_request_var('host_template_id',7), 
            get_nfilter_request_var('description',$value[0]),
            trim(get_nfilter_request_var('hostname',$value[1])), 
            get_nfilter_request_var('snmp_community',$value[2]), 
            get_nfilter_request_var('snmp_version',2),
            get_nfilter_request_var('snmp_username',"lcy"), 
            get_nfilter_request_var('snmp_password'),
            get_nfilter_request_var('snmp_port',161), 
            get_nfilter_request_var('snmp_timeout',500),
            (isset_request_var('disabled') ? get_nfilter_request_var('disabled') : ''),
            get_nfilter_request_var('availability_method',2), 
            get_nfilter_request_var('ping_method',1),
            get_nfilter_request_var('ping_port',23), 
            get_nfilter_request_var('ping_timeout',400),
            get_nfilter_request_var('ping_retries',1), 
            get_nfilter_request_var('notes'),
            get_nfilter_request_var('snmp_auth_protocol',"MD5"), 
            get_nfilter_request_var('snmp_priv_passphrase'),
            get_nfilter_request_var('snmp_priv_protocol',"DES"), 
            get_nfilter_request_var('snmp_context'),
            get_nfilter_request_var('snmp_engine_id'), 
            get_nfilter_request_var('max_oids',10),
            get_nfilter_request_var('device_threads',1), 
            get_nfilter_request_var('poller_id',1),
            get_nfilter_request_var('site_id',0), 
            get_nfilter_request_var('external_id'),
            get_nfilter_request_var('location'));
    }
}