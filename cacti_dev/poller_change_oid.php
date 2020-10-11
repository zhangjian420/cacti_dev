<?php
chdir(__DIR__);
require("./include/cli_check.php");

// 查找深信服模板的设备
$items = db_fetch_assoc("select pi.* from poller_item pi left join host h on pi.host_id = h.id where h.host_template_id = 9 and rrd_name in('traffic_in','traffic_out')");
foreach ($items as $item){
    // 如果是获取某个端口流量，但是这个端口的值还没有改变，需要查询端口的信息，然后去修改这个oid的值。如果以这个oid开头，说明已经修改过了，可以正常获取数据了
    if(strpos($item["arg1"], ".1.3.6.1.4.1.35047.2.1.8.0.") == false){
        $field = db_fetch_row("select hsc.* from host_snmp_cache hsc left join data_local dl 
            on dl.host_id = hsc.host_id and hsc.snmp_query_id = dl.snmp_query_id and dl.snmp_index = hsc.snmp_index 
            where hsc.field_name = 'ifDescr' and dl.id = " . $item["local_data_id"]);
        if (!empty($field) && !empty($field["field_value"])) {
            if(preg_match('/\d+/',$field["field_value"],$arr)){
                if($item["rrd_name"] == "traffic_in"){
                    $way = 1;
                }else if($item["rrd_name"] == "traffic_out"){
                    $way = 0;
                }
                db_execute("update poller_item set arg1 = '.1.3.6.1.4.1.35047.2.1.8.0.".$way.".".$arr[0].".0' 
                    where local_data_id = " .$item["local_data_id"] . " and rrd_name = '".$item["rrd_name"]."'");
            }
        }
    }
}