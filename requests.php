<?php
header('Access-Control-Allow-Origin: *');

include_once('common.php');
include_once('config_db.php');
include_once('db_methods.php');
include_once('json_response_manager.php');

if(!isset($_REQUEST['func'])){
    exit();
};

$func = $_REQUEST['func'];
$db = config_db();
$api = new api($db);
$response_manager = new json_response_manager;

switch($func){
    case "new_log": 
        $status =$api->new_log($_REQUEST['content']);
        $response_manager->test('inserting new log was not successful',$status);
        break;
    case "delete_database":
        $status = $api->delete_database();
        $response_manager->test("deleting database was not successful",$status);
        break;
    case 'get_logs':
        $logs = $api->get_logs();
        $response_manager->set_data($logs);
        break;
    case 'new_transaction':
        $api->new_transaction(
            $_REQUEST['username'],
            (int)$_REQUEST['amount'],
            $_REQUEST['info'],
            (int)$_REQUEST['plan_id'],
            $_REQUEST['one_percent_for_team'] == "true" ? true : false
        );
        break;
    case 'get_transactions':
        $transactions = $api->get_transactions();
        $response_manager->set_data($transactions);
        break;
    case 'delete_transaction':
        $status = $api->delete_transaction((int)$_REQUEST['transaction_id']);
        break;
    case 'delete_transactions':
        $status = $api->delete_transactions();
        break;
    case 'new_user':
        $status = $api->new_user($_REQUEST['username']);
        break;
    case 'get_users':
        $status = $api->get_users();
        $response_manager->set_data($status);
        break;
    case 'delete_users':
        $status = $api->delete_users();
        break;
    case 'delete_user':
        $api->delete_user($_REQUEST['username']);
        break;
    case 'is_username_available':
        $res = $api->is_username_available($_REQUEST['username']);
        $response_manager->set_data($res);
        break;
    case 'user_exists':
        $res = $api->does_user_exist($_REQUEST['username']);
        $response_manager->set_data([
            "user_exists"=>$res
        ]);
        break;
    case 'make_user_admin':
        $api->make_user_admin($_REQUEST['username'],$_REQUEST['password']);
        break;
    case 'verify_admin_password':
        $res = $api->verify_admin_password($_REQUEST['username'],$_REQUEST['password']);
        $response_manager->set_data([
            "admin_password_was_correct"=>$res
        ]);
        break;
    case 'change_admin_password':
        //todo : make sure about this and its func 
        $res = $api->change_admin_password($_REQUEST['username'],$_REQUEST['old_password'],$_REQUEST['new_password']);
        break;
    case 'get_admins':
        $res = $api->get_admins();
        $response_manager->set_data($res);
        break;
    case 'new_support_message':
        $res = $api->new_support_message($_REQUEST['username'],$_REQUEST['subject'],$_REQUEST['content']);
        break;
    case 'is_support_message_open':
        $res = $api->is_support_message_open((int)$_REQUEST['support_message_id']);
        $response_manager->set_data($res);
        break;
    case 'toggle_support_message_status':
        $api->toggle_support_message_status((int)$_REQUEST['support_message_id']);
        break;
    case 'close_support_message':
        $api->close_support_message((int)$_REQUEST['support_message_id']);
        break;
    case 'delete_support_messages':
        $api->delete_all_support_messages();
        break;
    case 'delete_support_message':
        $api->delete_support_message((int)$_REQUEST['support_message_id']);
        break;
    case 'get_support_messages':
        $res = $api->get_support_messages();
        $response_manager->set_data($res);
        break;
    case 'get_support_message':
        $res = $api->get_support_message((int)$_REQUEST['support_message_id']);
        $response_manager->set_data($res);
        break;
    case 'new_plan':
        $starter_username = $_REQUEST['starter_username'];
        $final_amount_as_rial = (int)$_REQUEST['final_amount_as_rial'];
        $title = $_REQUEST['title'];
        $description = $_REQUEST['description'];
        $res = $api->new_plan($starter_username,$title,$description,$final_amount_as_rial);
        break;
    case 'finish_plan':
        $res = $api->finish_plan((int)$_REQUEST['plan_id']);
        break;
    case 'get_plan_transactions':
        $res = $api->get_plan_transactions((int)$_REQUEST['plan_id']);
        $response_manager->set_data($res);
        break;
    case 'get_plan_data':
        $res = $api->get_plan_data((int)$_REQUEST['plan_id']);
        $response_manager->set_data($res);
        break;
    case 'delete_plans':
        $res = $api->delete_plans();
        break;
    case 'delete_plan':
        $res = $api->delete_plan((int)$_REQUEST['plan_id']);
        break;
    case 'get_plan_ids':
        $res = $api->get_plan_ids();
        $response_manager->set_data($res);
        break;
    case 'get_plans':
        $res = $api->get_plans();
        $response_manager->set_data($res);
        break;
    case 'get_last_plan_id':
        $res = $api->get_last_plan_id();
        $response_manager->set_data($res);
        break;
    case 'get_last_plan_data':
        $res = $api->get_last_plan_data();
        $response_manager->set_data($res);
        break;
    case "subscribe_to_sms":
        $result = $api->subscribe_to_sms($_REQUEST["phone_number"]);
        $response_manager->test("mysql query was not successful and its return value was false",$result);
        break;
    default:
        break;
}
$response_manager->done();
