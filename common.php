<?php
function get_mysql_table($db_instance,$table_name){
    $query = "select * from $table_name";
    $tmp = [];
    $results = $db_instance->query($query);
    while($row = mysqli_fetch_assoc($results)){
        $tmp[] = $row;
    };
    return $tmp;
};
function drop_table($db_instance,$table_name){
    $q = "drop table if exists $table_name";
    return $db_instance->query($q);
};
function append_two_arrays($a1,$a2){
    //todo its name is not good
    $tmp = [];
    foreach ($a1 as $key => $value) {
        $tmp[] = $value;
    }
    foreach ($a2 as $key => $value) {
        $tmp[] = $value;
    }
    return $tmp;
}
function last_item($array){
    $count = count($array);
    if($count != 0){
        return $array[$count-1];
    }else{
        return false;
    };
}
