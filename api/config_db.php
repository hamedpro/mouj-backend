<?php
function config_db(){
    $env_vars_file = fopen('../env_vars.json',"r");
    $env_vars = json_decode(fread($env_vars_file,filesize('../env_vars.json')));

    $mysql_username = $env_vars->mysql_username;
    $mysql_password = $env_vars->mysql_password;
    $mysql_database_name = $env_vars->mysql_database_name;
    $mysql_hostname = $env_vars->mysql_hostname;
    
    $db = new mysqli($mysql_hostname,$mysql_username,$mysql_password);
    $query = "create database if not exists $mysql_database_name" ; //todo:make sure about utf-8 support 
    $db->query($query);
    $db->close();

    $db = new mysqli($mysql_hostname,$mysql_username,$mysql_password,$mysql_database_name);
    $query = "create table if not exists users(
        id int(10) primary key auto_increment,
        username varchar(100),
        is_admin varchar(100),
        password varchar(100),
        phone_number varchar(15)
    )";
    if(!$db->query($query)){
        echo $db->error;
    };
    $query = "create table if not exists sms_subscribers(
        id int(10) primary key auto_increment,
        phone_number varchar(20)
    )";
    $db->query($query);
    
    $query = "create table if not exists transactions(
        id int(10) primary key auto_increment,
        username varchar(100),
        amount int(10),
        info varchar(500),
        plan_id int(6),
        one_percent_for_team varchar(50)
    )";
    //one percent for mouj carry a varchar ('true' or "false")
    $db->query($query);

    $query = "create table if not exists support_messages(
        id int(5) primary key auto_increment,
        username varchar(50),
        subject varchar(100),
        content varchar(500),
        status varchar(100)
    )";
    $db->query($query);

    $query = "create table if not exists plans(
        id int(5) primary key auto_increment,
        title varchar(100),
        description varchar(500),
        starter_username varchar(100),
        status varchar(100),
        final_amount_as_rial int(10),
        start_date varchar(100),
        end_date varchar(100)

    )";
    $db->query($query);

    $query = "create table if not exists logs(
        id int(5) primary key auto_increment,
        content varchar(500) 
    )";
    $db->query($query);
    return $db;
}