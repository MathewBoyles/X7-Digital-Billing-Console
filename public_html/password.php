<?php
  define("PAGE_ID", "password");
  define("PAGE_TITLE", "Password");
  define("ACTIVE_NAV", "DISABLED");
  require("../server_private/include.php");

  $app->login_required();

  if(isset($_POST["password"]) && isset($_POST["new_password"]) && isset($_POST["new_password_confirm"])):
    $login_error = false;

    if(!$config["password_verify"]($_POST["password"], $me->info["password"])) $login_error = "error";
    else if($_POST["new_password"] != $_POST["new_password_confirm"]) $login_error = "match";
    else if(!$app->password_security($_POST["new_password"])) $login_error = "security";
    else $link->query("UPDATE `".$config["mysql"]["prefix"]."users` SET `password_temp` = '0', `password_timestamp` = ".time().", `password` = '".$config["password_hash"]($_POST["new_password"])."' WHERE `id` = '".$me->info["id"]."'");

    header("location: /".($login_error?("password?msg=".$login_error):"settings?msg=password"));
    exit;
  endif;

  $messages = array(
    "error" => array(
      "danger",
      "Invalid password."
    ),
    "match" => array(
      "danger",
      "Your new password does not match the confirmation."
    ),
    "security" => array(
      "danger",
      "Your new password must contain at least 8 characters and must include one number and one letter."
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

<?PHP if(isset($_GET["msg"]) && isset($messages[$_GET["msg"]])): ?>
  <div class="alert alert-<?=$messages[$_GET["msg"]][0];?> alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <?=$messages[$_GET["msg"]][1];?>
  </div>
<?PHP endif; ?>
<?PHP if($me->info["password_temp"]): ?>
  <div class="card card-main">
    <div class="card-body text-sm">
      You are currently using a temporary password. Please choose a new password before proceeding.
    </div>
  </div>
<?PHP endif; ?>
  <div class="card card-main">
    <div class="card-body">
      <form method="POST" action="/password">
        <div class="form-group">
          <label for="login_password">Current Password</label>
          <input type="password" name="password" class="form-control" id="login_password" placeholder="Password">
        </div>

        <div class="form-group">
          <label for="login_password_new">New Password</label>
          <input type="password" name="new_password" class="form-control" id="login_password_new" placeholder="New Password">
        </div>

        <div class="form-group">
          <label for="login_password_confirm">New Password (again)</label>
          <input type="password" name="new_password_confirm" class="form-control" id="login_password_confirm" placeholder="New Password">
        </div>

        <div class="text-right">
          <a href="/<?=$me->info["account_ready"]?"settings":"logout";?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>

    </div>
  </div>
</div>

<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
