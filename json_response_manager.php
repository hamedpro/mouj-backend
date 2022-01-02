<?php
class json_response_manager {
    public $errors = [];
    public $data = [];
    public function test($error_message,$test_bool){
        if(!$test_bool){
            $this->errors[] = $error_message;
        };
    }
    public function set_data($data){
        $this->data = $data;
    }
    public function done(){
        echo json_encode([
            'data'=>$this->data,
            'errors'=>$this->errors
        ]);
        exit();
    }
}


/* 
usage is like =>

$rm = new json_response_manager;
$rm->test("test done",function(){return true;});
$rm->test("test which fails",function(){return false;});
$rm->set_data([1,2,2,3]);
$rm->done();

 */