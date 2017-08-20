<?php
  define("PAGE_ID", "login");
  define("PAGE_TITLE", "Log In");
  define("ACTIVE_NAV", "DISABLED");
  require("../server_private/include.php");

  if($me !== false) {
    header("location: /");
    exit;
  }

  if(isset($_POST["email"]) && isset($_POST["password"])) {
    $login_success = false;

    if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
      $validate_user = $link->query("SELECT * FROM `".$config["mysql"]["prefix"]."users` WHERE `email` = '".$_POST["email"]."'");
      if($validate_user->num_rows > 0){
        $validate_user_info = mysqli_fetch_array($validate_user, MYSQLI_ASSOC);

        if($config["password_verify"]($_POST["password"], $validate_user_info["password"])) {
          if(!$validate_user_info["has_access"]) {
            header("location: /login?msg=disabled");
            exit;
          }

          $login_success = true;

          $_SESSION["user_id"] = $validate_user_info["login_id"];
          $_SESSION["user_as"] = false;
        }
      }
    }

    header("location: /".($login_success?"":"login?msg=error"));
    exit;
  }

  $messages = array(
    "loggedout" => array(
      "success",
      "You have been logged out."
    ),
    "error" => array(
      "danger",
      "Invalid email/password combination."
    ),
    "disabled" => array(
      "danger",
      "Your account is currently disabled."
    )
  );

  $app->tmpl("top"); ?>

<div id="login_form">
  <div class="logo-block">
    <a href="/">
      <img src="/img/logo.png">
      <p>Billing Console</p>
    </a>
  </div>

<?PHP if(isset($_GET["msg"]) && isset($messages[$_GET["msg"]])) { ?>
  <div class="alert alert-<?=$messages[$_GET["msg"]][0];?> alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <?=$messages[$_GET["msg"]][1];?>
  </div>
<?PHP } ?>

  <div class="card card-main">
    <div class="card-body">
      <form method="POST" action="/login">
        <div class="form-group">
          <label for="login_email">Email address</label>
          <input type="email" name="email" class="form-control" id="login_email" placeholder="Enter email">
        </div>
        <div class="form-group">
          <label for="login_password">Password</label>
          <input type="password" name="password" class="form-control" id="login_password" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-primary">Log In</button>
        <a href="" class="forgot-password">Forgot Password?</a>
      </form>

    </div>
  </div>

  <div class="card card-main">
    <div class="card-body text-sm">
      Welcome to the new <strong>X7 Digital Billing Console</strong>, which is replacing the Mathew Boyles Console.
      <br /><br />
      This console is where you may manage your subscriptions and payments. Please visit the <a href="https://x7digital.com/admin">X7 Digital Admin Console</a> if you wish to manage your website or email accounts.
    </div>
  </div>
</div>

<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
