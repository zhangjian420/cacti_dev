<?php
chdir(__DIR__);
require("./include/cli_check.php");
include_once($config['base_path'] . '/lib/rrd.php');
include_once($config['base_path'] . '/rrd_util_functions.php');




/*
$max1 = get_max_traffic_by_graph(187,$start_time,$end_time);
cacti_log("max1 = " .$max1);
$max2 = get_max_traffic_by_data(213,$start_time,$end_time);
cacti_log("max2 = " .$max2);
*/

// $value1 = get_traffic_detail_by_data(213,$start_time,$end_time,300);
#cacti_log("value1 = " . json_encode($value1));

// $is = array(9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29);
// foreach ($is as $i){
//     if ($i == 9) {
//         $i = "09";
//     }
//     $end_time = strtotime("2020-07-".$i);
//     $start_time = $end_time - 86400;
//     $value1 = get_traffic_detail_by_graph(1752,$start_time,$end_time,300);
//     $value2 = get_traffic_detail_by_graph(1756,$start_time,$end_time,300);
    
//     $value = array();
//     foreach ($value1 as $k => $v){
//         if(key_exists($k, $value2)){
//            $value[$k] = $v + $value2[$k];
//         }
//     }
//     $f1 = fopen("json_"."2020-07-".$i.".txt", "w");
//     fwrite($f1, json_encode($value));
//     fclose($f1);
    
//     /*
//     $f2 = fopen("json_1756_"."2020-07-".$i, "w");
//     fwrite($f2, json_encode($value2));
//     fclose($f2);*/
// }
#$value2 = get_traffic_detail_by_graph(187,$start_time,$end_time,300);

$new1 = get_max_traffic_by_graph(3604,1596211200,1598889600);
cacti_log("new1 = " .json_encode($new1));
//$new2 = get_new_traffic_by_data(4639);
//cacti_log("new2 = " .$new2);
