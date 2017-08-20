<?php
  define("PAGE_ID", "card");
  define("PAGE_TITLE", "Card");
  define("ACTIVE_NAV", "DISABLED");
  require("../server_private/include.php");

  $app->login_required();

  if($me->info["account_ready"]){
    $cards = $me->cards();
    if(count($cards) >= 5){
      header("location: /settings?msg=cards.max");
      exit;
    }
  }

  if(isset($_POST["action"]) && isset($_POST["password"]) && isset($_POST["token"])) {
    header("Content-Type: text/json");
    $return_data = array("status" => "error", "message" => "An error occurred.");

    if(!$config["password_verify"]($_POST["password"], $me->info["password"])) $return_data["message"] = "Invalid password.";
    else {
      $customer = $me->stripe();
      $customer->sources->create(array("source" => $_POST["token"]));

      $link->query("UPDATE `".$config["mysql"]["prefix"]."users` SET `account_ready` = '".time()."' WHERE `id` = '".$me->info["id"]."'");
      $return_data = array("status"=>"success","message"=>"success");

      $headers = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/html; charset=iso-8859-1";
      $headers[] = "To: ".$config["rep"]["name"]." <".$config["rep"]["email"].">";
      $headers[] = "From: X7 Digital <admin@x7digital.com>";
      mail($config["rep"]["email"], "X7 Digital Billing Console - Account Activated", "<strong>" . $me->info["name"] . " (" . $me->info["email"] . ")</strong> activated their X7 Digital Billing Console account.", implode("\r\n", $headers));
    }

    die(json_encode($return_data));
  }

  $app->tmpl("top"); ?>

<div id="login_form">
  <div class="logo-block">
    <a href="/">
      <img src="/img/logo.png">
      <p>Billing Console</p>
    </a>
  </div>
  <div class="card card-main">
    <div class="card-body text-sm">
      <?=$me->info["account_ready"]?"You are adding a new card to your account.":"You have not yet added a debit/credit card to your account. Please add a card to proceed.";?>
    </div>
  </div>
  <div class="alert alert-danger card-main" role="alert" id="add_card_alert"></div>
  <div class="card card-main">
    <div class="card-body">
      <form method="POST" action="/card" id="add_card">
        <div id="add_card_msg"></div>

        <div class="form-group">
          <label for="addcard_name">Name on card</label>
          <input type="text" name="cardname" class="form-control" id="addcard_name" required>
        </div>

        <div class="form-group">
          <label for="addcard_number">Card number</label>
          <input type="text" name="creditcard" class="form-control" id="addcard_number" required>
        </div>

        <div class="form-group">
          <label for="addcard_expiry">Card expiry</label>
          <div class="row">
            <div class="col-sm-6">
              <select class="form-control" name="cardexpiry_month" id="addcard_expiry" required>
                <option selected disabled>Select month</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
              </select>
            </div>
            <div class="col-sm-6">
              <select class="form-control" name="cardexpiry_year" required>
                <option selected disabled>Select year</option>
<?PHP for($i=date('Y');$i<=(date('Y')+20);$i++){ ?>
                <option value="<?=$i;?>"><?=$i;?></option>
<?PHP } ?>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="addcard_cvv">CVV/CVC</label>
          <input type="text" name="cvv" class="form-control" aria-describedby="cvvHelp" id="addcard_cvv" required>
          <small id="cvvHelp" class="form-text text-muted">The 3-digit code on the back.</small>
        </div>

        <div class="form-group">
          <label for="login_password">Password</label>
          <input type="password" name="password" class="form-control" id="login_password" placeholder="Password" required>
        </div>
        <div class="text-right">
          <a href="/<?=$me->info["account_ready"]?"settings":"logout";?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card card-main border-success">
    <div class="card-body text-sm text-success">
      Card details are securely managed by <a href="https://stripe.com/" target="_blank">Stripe</a>.
    </div>
  </div>

<?PHP if(!$me->info["account_ready"]){ ?>
  <div class="card card-main">
    <div class="card-body text-sm">
      You will <strong>not</strong> yet be charged. You will be able to view and authorize payments after activation.
    </div>
  </div>
<?PHP } ?>
</div>

<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
