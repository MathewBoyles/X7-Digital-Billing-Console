<?php
  $config = array(
    "mysql" => array(
      "hostname" => "",
      "username" => "",
      "password" => "",
      "database" => "",
      "prefix" => ""
    ),
    "stripe" => array(
      "public" => "",
      "secret" => ""
    ),
    "rep" => array(
      "name" => "",
      "email" => ""
    ),
    "password_verify" => function($input_password, $check_password){

    },
    "password_hash" => function($input_password){

    }
  );
?>
