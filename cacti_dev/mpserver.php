<?php
/*
 * 处理小程序的请求服务
*/

include_once('./include/global.php');
include_once('./lib/api_device.php');
include_once('./lib/api_graph.php');
include_once('./lib/api_data_source.php');
include_once('./lib/data_query.php');
include_once('./lib/utility.php');
include_once('./lib/rrd.php');

/* set default action */
set_default_action();
$_SESSION['sess_error_fields'] = null;
$rows = 30;
switch (get_request_var('action')) {
    //-------------------------------站点处理开始
    case 'site_list':
        $sql = "select id,name from sites";
        if (get_request_var('name') != '') {
            $sql_where = "WHERE (name LIKE '%" . get_request_var('name') . "%')";
        } else {
            $sql_where = '';
        }
        $sites = db_fetch_assoc($sql . " " . $sql_where . " order by id desc");
        print json_encode($sites);
        break;
    case "site_save":
        $save = array();
        $save['id']             = get_filter_request_var('id');
        $save['name']           = get_nfilter_request_var('name');
        $save['notes']          = get_nfilter_request_var('notes');
        $site_id = sql_save($save, 'sites');
        print json_encode(array("site_id" => $site_id));
        break;
    case "site_get":
        $sql = "select id,name,notes from sites where id = " . get_filter_request_var("id");
        $site = db_fetch_row($sql);
        print json_encode($site);
        break;
    case "site_delete":
        $sql = "delete from sites where id = " . get_filter_request_var("id");
        db_execute($sql);
        break;
    //-------------------------------站点处理结束
    
    //-------------------------------设备处理开始
    case 'host_list':
        $sql = "select h.id,h.hostname,h.description,h.status,s.name as site_name 
        from host as h left join sites as s on h.site_id = s.id ";
        if (get_request_var('name') != '') {
            $sql_where = "WHERE (h.hostname LIKE '%" . get_request_var('name') 
            . "%' OR h.description LIKE '%" . get_request_var('name') . "%' OR h.id LIKE '%" . get_request_var('name') . "%')";
        } else {
            $sql_where = "";
        }
        $sql_order = " order by h.id desc";
        $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page',1)-1)) . ',' . $rows;
        
        $sites = db_fetch_assoc($sql . " " . $sql_where . $sql_order . $sql_limit);
        print json_encode($sites);
        break;
    case "host_save":
        get_filter_request_var('id');
        get_filter_request_var('host_template_id');
        $host_id = api_device_save(get_nfilter_request_var('id',0), get_nfilter_request_var('host_template_id',7), get_nfilter_request_var('description'),
            trim(get_nfilter_request_var('hostname')), get_nfilter_request_var('snmp_community'), get_nfilter_request_var('snmp_version',2),
            get_nfilter_request_var('snmp_username',"lcy"), get_nfilter_request_var('snmp_password'),
            get_nfilter_request_var('snmp_port'), get_nfilter_request_var('snmp_timeout',500),
            (isset_request_var('disabled') ? get_nfilter_request_var('disabled') : ''),
            get_nfilter_request_var('availability_method',2), get_nfilter_request_var('ping_method',1),
            get_nfilter_request_var('ping_port',23), get_nfilter_request_var('ping_timeout',400),
            get_nfilter_request_var('ping_retries',1), get_nfilter_request_var('notes'),
            get_nfilter_request_var('snmp_auth_protocol',"MD5"), get_nfilter_request_var('snmp_priv_passphrase'),
            get_nfilter_request_var('snmp_priv_protocol',"DES"), get_nfilter_request_var('snmp_context'),
            get_nfilter_request_var('snmp_engine_id'), get_nfilter_request_var('max_oids',50),
            get_nfilter_request_var('device_threads',1), get_nfilter_request_var('poller_id',1),
            get_nfilter_request_var('site_id'), get_nfilter_request_var('external_id'),
            get_nfilter_request_var('location'));
        print json_encode(array("host_id" => $host_id));
        break;
    case "host_get":
        $sql = "select h.id,h.hostname,h.description,h.status,site_id,snmp_community,snmp_port,location from host as h where id = " . get_filter_request_var("id");
        $host = db_fetch_row($sql);
        print json_encode($host);
        break;
    case "host_delete":
        $selected_items = array(get_filter_request_var("id"));
        api_device_remove_multi($selected_items, get_request_var('delete_type',2));
        break;
    //-------------------------------设备处理结束
    
    //-------------------------------图形处理开始
    case 'graph_list':
        $sql = "select local_graph_id as id,title_cache from graph_templates_graph where local_graph_id > 0";
        if (get_request_var('name') != '') {
            $sql_where = "WHERE (title_cache LIKE '%" . get_request_var('name')
             . "%' OR local_graph_id LIKE '%" . get_request_var('name') . "%')";
        } else {
            $sql_where = "";
        }
        $sql_order = " order by local_graph_id desc";
        $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page',1)-1)) . ',' . $rows;
        
        $graphs = db_fetch_assoc($sql . " " . $sql_where . $sql_order . $sql_limit);
        print json_encode($graphs);
        break;
    case "graph_save":
        
        break;
    case "graph_get":
        $sql = "select h.id,h.hostname,h.description,h.status,site_id,snmp_community,snmp_port,location from host as h where id = " . get_filter_request_var("id");
        $graph = db_fetch_row($sql);
        print json_encode($graph);
        break;
    case "graph_delete":
        $selected_items = array(get_filter_request_var("id"));
        api_delete_graphs($selected_items, get_filter_request_var('delete_type',1));
        break;
    case "graph_image":
        $graph_data_array = array(
            "graph_start"=>-86400,
            "graph_end"=> '-' . read_config_option('poller_interval'),
            "graphv" => true,
            "image_format" => "png"
        );
        cacti_log(json_encode($graph_data_array));
        $output = rrdtool_function_graph(get_request_var('id'), 0, $graph_data_array);
        $image_begin_pos  = strpos($output, "image = ");
        $image_data_pos   = strpos($output, "\n" , $image_begin_pos) + 1;
        
        $oarray = array();
        $oarray['image'] = base64_encode(substr($output, $image_data_pos));
        $header_lines     = explode("\n", substr($output, 0, $image_begin_pos - 1));
        foreach ($header_lines as $line) {
            $parts = explode(" = ", $line);
            $oarray[$parts[0]] = trim($parts[1]);
        }
        print json_encode($oarray);
        break;
    //-------------------------------图形处理结束
    default:
        cacti_log("default");
        break;
}
