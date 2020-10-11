<?php
chdir(__DIR__);
require("./include/cli_check.php");
require("./lib/data_query.php");

// 通过定时器完成一些bug的修改

// bug1： 不知道怎么回事儿，graph_local和data_local 中 snmp_index 会莫名其妙的为空  -- 解决开始

// 查询所有的bug类型的graph_local，注意聚合图形graph_template_id、host_id、snmp_query_id都是0 snmp_index=''
$graph_locals = db_fetch_assoc("select * from graph_local where 
    graph_template_id != 0 and host_id != 0 and snmp_query_id != 0 and snmp_index = ''");
foreach ($graph_locals as $graph_local){
    $local_graph_id = $graph_local["id"];
    $data_query_id = $graph_local["snmp_query_id"];
    $host_id = $graph_local["host_id"];
    
    $field = data_query_field_list(db_fetch_cell_prepared('SELECT dtd.id
		FROM graph_templates_item AS gti
		INNER JOIN data_template_rrd AS dtr
		ON gti.task_item_id=dtr.id
		INNER JOIN data_template_data AS dtd
		ON dtr.local_data_id=dtd.local_data_id
		WHERE gti.local_graph_id = ?
		LIMIT 1',  array($local_graph_id)));
    
    $current_index = data_query_index($field['index_type'], $field['index_value'], $host_id, $data_query_id);
    // 说明根据ifName没有找到snmp_index
    if(empty($current_index)){
        
    }
    
    //print_r("graph_local_id = " . $graph_local_id . " data_template_data_id = " . $data_template_data_id ."\r\n") ;
    print_r("graph_local_id = " . $local_graph_id ." field = " . json_encode($field) . " current_index = " .$current_index ."\r\n") ;
}


