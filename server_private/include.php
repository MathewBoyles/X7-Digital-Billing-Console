<?php
  require("config.php");
  require("stripe-php-5.1.3/init.php");
  \Stripe\Stripe::setApiKey($config["stripe"]["secret"]);

  class APP {
    public function login_required(){
      GLOBAL $me;
      if($me === false) {
        header("location: /login");
        exit;
      }
      if($me->info["password_temp"] && PAGE_ID !== "password") {
        header("location: /password");
        exit;
      }
      if(!$me->info["account_ready"] && !$me->info["password_temp"] && PAGE_ID !== "card") {
        header("location: /card");
        exit;
      }
    }

    public function tmpl($tmpl, $data = array()){
      GLOBAL $config, $app, $me;

      include "tmpl/" . $tmpl . ".php";
    }

    public function format_size($size){
      $mod = 1024;
      $units = explode(' ','B KB MB GB TB PB');
      for ($i = 0; $size >= $mod; $i++) {
        $size /= $mod;
      }
      return round($size, 2) . ' ' . $units[$i];
    }

    public function password_security($password){
      $has_error = false;
      if (strlen($password) < 8) $has_error = true;
      if (!preg_match("#[0-9]+#", $password)) $has_error = true;
      if (!preg_match("#[a-zA-Z]+#", $password))  $has_error = true;
      return !$has_error;
    }

    public function __construct(){
      GLOBAL $me, $link, $config;

      if(isset($_SESSION["user_id"])) {
        $validate_user = $link->query("SELECT * FROM `".$config["mysql"]["prefix"]."users` WHERE `login_id` = '".$_SESSION["user_id"]."'");
        if($validate_user->num_rows > 0){
          $validate_user_info = mysqli_fetch_array($validate_user, MYSQLI_ASSOC);
          if($validate_user_info["account_type"] == "admin" && isset($_SESSION["user_as"])) $me = new USER($_SESSION["user_as"]?$_SESSION["user_as"]:$validate_user_info["id"]);
          else $me = new USER($validate_user_info["id"]);
          $validate_user->close();
        } else {
          unset($_SESSION["user_id"]);
          unset($_SESSION["user_as"]);
        }
      }
    }

    public function end(){
      GLOBAL $link;
      $link->close();
    }
  }

  class USER {
    public function stripe(){
      return \Stripe\Customer::retrieve($this->info["customer_id"]);
    }

    public function payments(){
      GLOBAL $link, $config;

      $return_array = array(
        "items" => array(),
        "owing" => 0,
        "total" => 0,
        "pending" => 0,
        "overdue" => 0
      );

      if($payments_query = $link->query("SELECT * FROM `".$config["mysql"]["prefix"]."invoices` WHERE `user` = '".$this->uid."' ORDER BY `paid` ASC, `id` DESC")) {
        while($item = mysqli_fetch_array($payments_query, MYSQLI_ASSOC)) {
          if(!$item["paid"]) {
            $return_array["owing"] += $item["amount"];
            $return_array["pending"]++;
            if($item["due"] < time()) $return_array["overdue"]++;
          }
          $return_array["total"]++;
          array_push($return_array["items"], $item);
        }
        $payments_query->close();
      }

      return $return_array;
    }

    public function subscriptions($data = array()){
      $subscriptions_data = array(
        "customer" => $this->info["customer_id"],
        "limit" => 100,
        "status" => "all"
      );
      $subscriptions_array = \Stripe\Subscription::all(array_merge($subscriptions_data, $data));
      return $subscriptions_array["data"];
    }

    public function invoices($data = array()){
      $invoices_data = array(
        "customer" => $this->info["customer_id"],
        "limit" => 100
      );
      $invoices_array = \Stripe\Invoice::all(array_merge($invoices_data, $data));
      return $invoices_array["data"];
    }

    public function cards($data = array()){
      $cards_data = array(
        "object" => "card",
        "limit" => 100
      );
      $cards_array = \Stripe\Customer::retrieve($this->info["customer_id"])->sources->all(array_merge($cards_data, $data));
      return $cards_array["data"];
    }

    public function __construct($uid) {
      GLOBAL $link, $config;

      $this->uid = $uid;
      $this->downloads_limit = 500 * 1048576;

      $user_info = $link->query("SELECT * FROM `".$config["mysql"]["prefix"]."users` WHERE `id` = '".$uid."'");
      $this->info = $user_info->fetch_array();
      $user_info->close();
    }
  }

  date_default_timezone_set("NZ");
  session_name("X7SESSION");
  session_start();

  $link = mysqli_connect($config["mysql"]["hostname"], $config["mysql"]["username"], $config["mysql"]["password"], $config["mysql"]["database"]);

  if (!$link) die("CRITICAL ERROR: DATABASE CONNECTION FAILED (" . mysqli_connect_errno() . ")");

  $me = false;
  $app = new APP;
?>
