<?php
chdir(__DIR__);
require("./include/cli_check.php");
include_once($config['base_path'] . '/lib/rrd.php');

$rra_path = "./rra";
$temp = scandir($rra_path);
db_execute("truncate rrd_check_result");
foreach($temp as $host_id){
    if (is_dir($rra_path . "/" . $host_id)) {
        foreach (scandir($rra_path . "/" . $host_id) as $rra_file_name){
            $rra_abs_file = $rra_path . "/" . $host_id . "/" . $rra_file_name;
            if(is_file($rra_abs_file)){
                $local_data_id = explode(".", $rra_file_name)[0];
                if ($argc == 2 && $local_data_id != $argv[1]) { // 用户传入local_data_id 但是 和文件的 id不一致，不做处理。单独处理用户传入的local_data_id
                    continue;
                }
                
                // 从rrdtool中得到step
                $rra_step_result = shell_exec("rrdtool info " . $rra_abs_file . " | grep step | awk -F ' = ' '{print $2}'");
                $rra_traffic_in_result = shell_exec("rrdtool info " . $rra_abs_file . " | grep heartbeat | grep traffic_in | awk -F ' = ' '{print $2}'");
                $rra_traffic_out_result = shell_exec("rrdtool info " . $rra_abs_file . " | grep heartbeat | grep traffic_out | awk -F ' = ' '{print $2}'");
                // print_r($rra_abs_file . " " . trim($rra_step_result) . " " . trim($rra_traffic_in_result) . " " . trim($rra_traffic_out_result) . " \n");
                
                $data_template_data = db_fetch_row("select rrd_step,data_source_profile_id from data_template_data where local_data_id = " . $local_data_id);
                if (empty($data_template_data)) {
                    continue;
                }
                $data_template_rrd = db_fetch_row("select max(case data_source_name when 'traffic_in' then rrd_heartbeat else 0 end) traffic_in,
                    max(case data_source_name when 'traffic_out' then rrd_heartbeat else 0 end) traffic_out from data_template_rrd
                     where local_data_id = " . $local_data_id . " group by local_data_id");
                if(empty($data_template_rrd)){
                    continue;
                }
                
                $save = array();
                $save["host_id"] = $host_id;
                $save["local_data_id"] = $local_data_id;
                $save["data_source_profile_id"] = $data_template_data["data_source_profile_id"];
                $save["step"] = $data_template_data["rrd_step"];
                $save["traffic_in_heartbeat"] = $data_template_rrd["traffic_in"];
                $save["traffic_out_heartbeat"] = $data_template_rrd["traffic_out"];
                
                $save["rrd_file_step"] = trim($rra_step_result);
                $save["rrd_traffic_in_heartbeat"] = trim($rra_traffic_in_result);
                $save["rrd_traffic_out_heartbeat"] = trim($rra_traffic_out_result);
                
                $save["should_step"] = $data_template_data["data_source_profile_id"] == 1 ? 300 : 60;
                $save["should_rrd_traffic_in_heartbeat"] = $data_template_data["data_source_profile_id"] == 1 ? 600 : 120;
                $save["should_rrd_traffic_out_heartbeat"] = $data_template_data["data_source_profile_id"] == 1 ? 600 : 120;
                
                $save["check_time"] = time();
                sql_save($save, "rrd_check_result");
            }
        }
    }
}