<?php
class api{
    public $db;
    public function __construct($db){
        $this->db = $db;
    }
    public function delete_database(){
        $query = "drop database if exists mouj";
        return $this->db->query($query);
    }
    public function new_log($content){
        $query = "insert into logs (content) values ('$content')";
        return $this->db->query($query);
    }
    public function get_logs(){
        return get_mysql_table($this->db,'logs');
    }
    
    public function new_transaction($username,$amount,$info,$plan_id,$one_percent_for_team){
        $one_percent_for_team_as_varchar = $one_percent_for_team ? "true" : "false";
        $query = "insert into transactions (amount,info,username,plan_id,one_percent_for_team) values ('$amount','$info','$username',$plan_id,'$one_percent_for_team_as_varchar')";
        return $this->db->query($query);
        // finish all other plans =>
        /*  $plans = $this->get_plans();
            foreach ($plans as $key => $value) {
            if($value->final_amount_as_rial<= $value->current_amount){
                $this->finish_plan(['plan_id'=>$value->id]);
            };
        }; */
    }
    public function get_transactions(){
        return get_mysql_table($this->db,'transactions');
    }
    public function delete_transaction($transaction_id){
        $query = "delete from transactions where id = $transaction_id";
        return $this->db->query($query);
    }

    public function delete_transactions(){
        return drop_table($this->db,'transactions');
    }
    public function new_user($username){
        $query = "insert into users (username) values ('$username')";
        return $this->db->query($query);
    }

    public function get_users(){
        return get_mysql_table($this->db,'users');
    }
    public function delete_users(){
        return drop_table($this->db,'users')?"true":"false";
    }
    public function delete_user($username){
        $q = "delete from users where username='$username'";
        return $this->db->query($q);
    }
    public function is_username_available($username){
        $query = "select * from users where username = '$username'";
        $results = $this->db->query($query);
        return mysqli_num_rows($results) === 0;
    }
    public function does_user_exist($username){
        return $this->is_username_available($username) == false;
    }
    public function make_user_admin($username,$password){
        /* if($this->does_user_exist($username) != true){
            $this->new_user(['username'=>$username]);
        }; */
        $q = "update users set is_admin = 'true' where username='$username'";
        $this->db->query($q);
        $q = "update users set password = '$password' where username='$username'";
        return $this->db->query($q);
    }
    public function change_admin_password($username,$old_password,$new_password){
        if($this->verify_admin_password($username,$old_password) != true){
            return "wrong_password";
        }
        $q = "update users set password = '$new_password' where username = '$username'";
        return $this->db->query($q);
    }
    public function get_admins(){
        $users = $this->get_users();
        $admins = [];
        foreach ($users as $key => $value) {
            if($value['is_admin'] == "true"){
                $admins[] = $value;
            }
        }
        return $admins;
    }
    public function verify_admin_password($username,$password){
        $q = "select password from users where username = '$username'";
        $q_results = $this->db->query($q);
        $row = mysqli_fetch_assoc($q_results);
        return $row['password'] == $password;
    }

    public function new_support_message($username,$subject,$content){
        $query = "insert into support_messages (username,subject,content,status) values ('$username','$subject','$content','open')";
        return $this->db->query($query);
    }
    // funcs and request gateway in done until here 
    public function toggle_support_message_status($support_message_id){
        if($this->is_support_message_open($support_message_id)){
            $query = "update support_messages set status = 'closed' where id = $support_message_id";
            return $this->db->query($query);
        }else{
            $query = "update support_messages set status = 'open' where id = $support_message_id";
            return $this->db->query($query);
        };
    }
    public function close_support_message($support_message_id){
        //todo make sure about error handling of this func 
        if(! $this->is_support_message_open($support_message_id)){
            return true;
        }else{
            $query = "update support_messages set status = 'closed' where id = $support_message_id";
            return $this->db->query($query);
        }
    }
    public function is_support_message_open($support_message_id){
        $query = "select status from support_messages where id = $support_message_id";
        return mysqli_fetch_assoc($this->db->query($query))['status'] == 'open';
    }
    
    public function delete_support_messages(){
        return drop_table($this->db,'support_messages');
        
    }
    public function delete_support_message($support_message_id){
        $q = "delete from support_messages where id = $support_message_id";
        return $this->db->query($q);
    }
    public function get_support_messages(){
        return get_mysql_table($this->db,'support_messages');
    }
    //done 
    public function get_support_message($support_message_id){
        $support_messages = $this->get_support_messages();
        foreach ($support_messages as $key => $value) {
            if($value['id'] == $support_message_id){
                return $value;
            };
        };
    }
    public function new_plan($starter_username,$title,$description,$final_amount_as_rial){
        $start_date = date("Y:M:D");
        $q = "insert into plans(title,description,starter_username,status,start_date,final_amount_as_rial) values ('$title','$description','$starter_username','open','$start_date',$final_amount_as_rial)";
        return $this->db->query($q);
    }
    //done
    public function finish_plan($plan_id){
        $q = "update plans set status = 'finished' where id=$plan_id";
        $this->db->query($q);

        $current_date = date('Y:M:D');
        $q = "update plans set end_date = '$current_date' where id=$plan_id";
        return $this->db->query($q);
    }
    public function get_plan_data($plan_id){
        $q = "select * from plans where id = $plan_id";
        $q_results = $this->db->query($q);
        $database_row = mysqli_fetch_assoc($q_results);
        $computed_data = [];
        //add computed props =>
        $plan_transactions = $this->get_plan_transactions($plan_id);
        $current_amount = 0;
        foreach ($plan_transactions as $key => $value) {
            $current_amount += (int)$value['amount'];
            
        }
        $computed_data["current_amount"] = $current_amount;
        $computed_data['amount_is_collected'] = $current_amount >= (int)$database_row['final_amount_as_rial'];
        //end of adding computed props
        
        $final_results = [];
        foreach ($database_row as $key => $value) {
            $final_results[$key] = $value;
        }
        foreach ($computed_data as $key => $value) {
            $final_results[$key] = $value;
        }
        return $final_results;
    }
    public function update_plan(){
        
    }
    public function delete_plans(){
        return drop_table($this->db,'plans');
    }
    public function delete_plan($plan_id){
        $q = "delete from plans where id=$plan_id";
        return $this->db->query($q);
    }
    public function get_plan_transactions($plan_id){
        $q = "select * from transactions where plan_id = $plan_id";
        $q_results = $this->db->query($q);
        $results = [];
        while($row = mysqli_fetch_assoc($q_results)){
            $results[] = $row;
        }
        return $results;
        //todo
    }
    public function get_plan_ids(){
        $q = "select * from plans";
        $q_results = $this->db->query($q);
        $results = [];
        while($row = mysqli_fetch_assoc($q_results)){
            $results[] = $row['id'];
        }
        return $results;
    }
    public function get_plans(){        
        return get_mysql_table($this->db,'plans');
    }
    public function get_last_plan_id(){
        return (int)last_item($this->get_plan_ids());
    }
    public function get_last_plan_data(){
        $last_plan_id = $this->get_last_plan_id();
        return $this->get_plan_data($last_plan_id);
    }
    public function subscribe_to_sms($phone_number){
        $q = "insert into sms_subscribers (phone_number) values ('$phone_number')";
        $result =  $this->db->query($q);
        return $result;
    }
}