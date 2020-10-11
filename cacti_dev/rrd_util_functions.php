<?php
/**
 * 根据图形ID获取一段时间内的流量数据，返回这段时间内 按照图形间隔 的每次流量值（出口或者入口流量，谁大取谁），流量单位是G
 */
function get_traffics_by_graph($local_graph_id,$start_time,$end_time){
    $graph_data_array = array("graph_start"=>$start_time,"graph_end"=>$end_time,"export_csv"=>true);
    $xport_meta = array();
    
    // 聚合图形获取数据
    $xport_array = rrdtool_function_xport($local_graph_id, 0, $graph_data_array, $xport_meta);
    $ret = array();
    if (!empty($xport_array["data"])) {
        foreach ($xport_array["data"] as $data){
            $traffic = max(array_values($data));
            if(!empty($traffic)){
                $ret[] = round($traffic/1000000000,2);
            }
        }
    }
    return $ret;
}

/**
 * 根据图形ID获取一段时间内的流量数据，返回这段时间流量的最大值（出口或者入口流量，谁大取谁），流量单位是G。
 */
function get_max_traffic_by_graph($local_graph_id,$start_time,$end_time){
    $datas  = get_traffics_by_graph($local_graph_id,$start_time,$end_time);
    if (empty($datas)) {
        return 0;
    }
    return max($datas);
}

/**
 * 根据图形ID，获取最新流量的值（出口或者入口流量，谁大取谁），流量单位是G
 */
function get_new_traffic_by_graph($local_graph_id,$step=60){
    $d = strtotime(date('Y-m-d H:i',time()))-$step;
    $datas  = get_traffics_by_graph($local_graph_id,$d-$step,$d);
    if (empty($datas)) {
        return 0;
    }
    return end($datas);
}

/**
 * 根据数据源ID获取一段时间内的流量数据，返回这段时间流量的最大值（出口或者入口流量，谁大取谁），流量单位是G。
 */
function get_max_traffic_by_data($local_data_id,$start_time,$end_time,$resolution = 86400){
    $max = 0;
    $result = rrdtool_function_fetch($local_data_id, $start_time, $end_time,$resolution,false,null,"MAX");
    if (!empty($result) && !empty($result["values"])) {
        foreach ($result["values"] as $data){
            $d1 = max(array_values($data));
            if($d1 > $max){
                $max = $d1;
            }
        }
    }
    return round($max/1000000000 * 8,2);
}

/**
 * 根据数据源ID，获取最新流量的值（出口或者入口流量，谁大取谁），流量单位是G------这个方法好像有错误
 */
function get_new_traffic_by_data($local_data_id){
    $max = 0;
    $d = strtotime(date('Y-m-d H:i',time()))-60;
    $result = rrdtool_function_fetch($local_data_id, $d-60,$d,0,false,null,"LAST");
    if (!empty($result) && !empty($result["values"])) {
        foreach ($result["values"] as $data){
            $d1 = end(array_values($data));
            if($d1 > $max){
                $max = $d1;
            }
        }
    }
    return round($max/1000000000 * 8,2);
}

/**
 * 根据图形ID获取一段时间内的详细数据浏览，格式为JSON {time1:value1,time2:value2} （出口或者入口流量，谁大取谁），流量单位是G
 */
function get_traffic_detail_by_graph($local_graph_id,$start_time,$end_time,$resolution = 86400){
    $graph_data_array = array("graph_start"=>$start_time,"graph_end"=>$end_time,"export_csv"=>true);
    $xport_meta = array();
    // 聚合图形获取数据
    $xport_array = rrdtool_function_xport($local_graph_id, 0, $graph_data_array, $xport_meta,$resolution);
    $step = $xport_array["meta"]["step"];
    // $start_time = $xport_array["meta"]["start"];
    $ret = array();
    // 如果是1分钟的粒度
    $col_name = "col1";
    if ($step == 60 && !empty($xport_array["data"])) { 
        foreach ($xport_array["data"] as $key => $value){
            if($value["col1"] < $value["col2"]){
                $col_name = "col2";
            }
            $time = (floor(($key-1) / 5) * $step * 5 + $start_time) . "";
            $max = $value[$col_name];
            if (empty($max) || $max == "NaN") {
                continue;
            }
            if (array_key_exists($time, $ret)) {
                $max_o = $ret[$time];
                if ($max > $max_o) {
                    $ret[$time] = round($max / 1000000000,2);
                }
            }else {
                $ret[$time] = round($max / 1000000000,2);
            }
        }
    }
    // 如果是5分钟的粒度
    if ($step == 300 && !empty($xport_array["data"])) {
        foreach ($xport_array["data"] as $key => $value){
            if($value["col1"] < $value["col2"]){
                $col_name = "col2";
            }
            $time = (($key-1) * $step + $start_time) . "";
            $max = $value[$col_name];
            if (empty($max) || $max == "NaN") {
                continue;
            }
            $ret[$time] = round($max / 1000000000,2);
        }
    }
    return $ret;
}

/**
 * 根据数据源ID获取一段时间内的详细数据浏览，格式为JSON {time1:value1,time2:value2} （出口或者入口流量，谁大取谁），流量单位是B。
 */
function get_traffic_detail_by_data($local_data_id,$start_time,$end_time,$resolution = 86400){
    $result = rrdtool_function_fetch($local_data_id, $start_time, $end_time,$resolution,false,null,"MAX");
    $ret = array();
    if (!empty($result) && !empty($result["values"])) {
        $values = $result["values"];
        $first_in = array_values($values[0])[0];
        $first_out = array_values($values[1])[0];
        if ($first_in > $first_out) {
            $ret = $result["values"][0];
        }else {
            $ret = $result["values"][1];
        }
    }
    array_pop($ret);
    return $ret;
}