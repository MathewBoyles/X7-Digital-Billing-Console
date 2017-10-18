<?php
  define("PAGE_ID", "payments");
  define("PAGE_TITLE", "Payments");
  define("ACTIVE_NAV", "payments");
  require("../server_private/include.php");

  $app->login_required();

  $payments = $me->payments();
  $cards = array();
  $show_msg = false;

  $view_payment = false;
  if(isset($_GET["id"])):
    if($_GET["id"]):
      $view_payment = "error";
      foreach($payments["items"] as $item):
        if($item["id"] == $_GET["id"]):
          $view_payment = true;
          $payment = $item;

          if(isset($_GET["print"])):
            $app->tmpl("modules/payment", $payment);
            die;
          endif;

          if(isset($_POST["action"]) && isset($_POST["card"]) && isset($_POST["password"])):
            if($_POST["action"] == "pay"):
              if($config["password_verify"]($_POST["password"], $me->info["password"])):
                $cards = $me->cards();
                $okay_card = false;
                foreach ($cards as $card):
                  if($_POST["card"] == $card["id"]):
                    $okay_card = $card["id"];
                    break;
                  endif;
                endforeach;
                if($okay_card):
                  $stripe_id = \Stripe\Charge::create(array(
                    "amount" => $payment["amount"],
                    "currency" => "nzd",
                    "customer" => $me->info["customer_id"],
                    "source" => $okay_card,
                    "description" => ("Payment #PEND-" . $payment["id"])
                  ));
                  $stripe_id = $stripe_id["id"];

                  $payment["paid"] = time();
                  $payment["stripe_id"] = $stripe_id;
                  $link->query("UPDATE `".$config["mysql"]["prefix"]."invoices` SET `paid` = '".$payment["paid"]."', `stripe_id` = '".$payment["stripe_id"]."' WHERE `id` = '".$payment["id"]."'");

                  $show_msg = array("success", "Payment successful.");
                else: $show_msg = array("danger", "An error occurred. Payment not authorized."); endif;
              else: $show_msg = array("danger", "Invalid password. Payment not authorized."); endif;
            endif;
          endif;

          break;
        endif;
      endforeach;
    endif;
  endif;

  $app->tmpl("top"); ?>

<?PHP if($show_msg !== false): ?>
  <div class="alert alert-<?=$show_msg[0];?> alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <?=$show_msg[1];?>
  </div>
<?PHP endif; ?>

<?PHP if($view_payment === true): ?>
  <div class="d-print-none back-top">
    <a href="/payments"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back to payments</a>
  </div>

  <div class="card card-main">
    <div class="card-header card-more"><?=$payment["title"];?> (#PEND-<?=$payment["id"];?>) <span class="btn btn-<?=$payment["paid"]?"success":"danger";?> btn-sm pull-right"><?=$payment["paid"]?"PAID":"UNPAID";?></span></div>
    <div class="card-body">
      <img src="/img/logo-black.png" width="200" />
      <hr />
      <div class="row">
        <div class="col-sm-6">
          <span class="text-muted">Customer:</span><br />
          <span><?=$me->info["name"];?></span><br />
          <span><?=$me->info["email"];?></span>
        </div>
        <div class="col-sm-6">
          <span class="text-muted">X7 Digital:</span><br />
          <span><?=$config["rep"]["name"];?></span><br />
          <span><?=$config["rep"]["email"];?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-main">
    <div class="card-header">Payment Details</div>
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div>
            <span class="text-muted">ID:</span> <?=$payment["id"];?>
          </div>
          <div>
            <span class="text-muted">Paid on:</span> <?=$payment["paid"]?date('m/d/Y', $payment["paid"]):"<em>Unpaid</em>";?>
          </div>
        </div>
        <div class="col-sm-6">
          <div>
            <span class="text-muted">Date:</span> <?=date('m/d/Y', $payment["date"]);?>
          </div>
          <div>
            <span class="text-muted">Due by:</span> <?=date('m/d/Y', $payment["due"]);?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-main">
    <table class="table">
      <tbody>
<?PHP
  $payment_lines = explode(";", $payment["description"]);
  foreach($payment_lines as $line):
    $line_info = explode("==", $line);
?>
        <tr>
          <td class="text-dark"><?=$line_info[0];?></td>
          <td class="text-right"><?PHP if(isset($line_info[1])): echo "$" . ($line_info[1]<0?"(":"") . number_format(($line_info[1]<0?(-$line_info[1]):$line_info[1])/100, 2) . ($line_info[1]<0?")":""); else:echo "&nbsp;";endif; ?></td>
        </tr>
<?PHP endforeach; ?>
        <tr>
          <td>&nbsp;</td>
          <td class="text-right text-muted">Total: <strong>$<?=number_format($payment["amount"]/100, 2);?></strong></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="card card-main d-print-none">
    <div class="card-header">Payment</div>
    <div class="card-body">
<?PHP if($payment["paid"]): ?>
      <div class="text-center text-muted">
        This invoice has been paid.
      </div>
<?PHP elseif($payment["offline_only"]): ?>
      <div class="text-center text-muted">
        <em>This payment may not be fulfilled via this billing console.</em>
      </div>
<?PHP else:
  if(count($cards) === 0) $cards = $me->cards();
  if(count($cards) === 0): ?>
      <div class="text-center text-muted">
        You have not yet added any cards to your account.
        <br /><a href="/card">Add Card</a>
      </div>
<?PHP else: ?>
      <form method="POST">
        <input type="hidden" name="action" value="pay" />
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label for="cardSelection">Card</label>
              <select class="form-control" aria-describedby="cardHelp" id="cardSelection" name="card">
<?PHP
  foreach($cards as $card): ?>
                <option value="<?=$card["id"];?>"><?=$card["name"].($card["name"]?" — ":"");?> <?=$card["brand"];?> ••••<?=$card["last4"];?></option>
<?PHP endforeach; ?>
              </select>
              <small id="cardHelp" class="form-text text-muted">Select your card to use for this payment. <a href="/settings">Manage cards</a></small>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label for="cardPassword">Password</label>
              <input type="password" class="form-control" aria-describedby="passwordHelp" id="cardPassword" placeholder="Password" name="password" required>
              <small id="passwordHelp" class="form-text text-muted">Enter your password to verify this payment.</small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 ml-auto">
            <button type="submit" class="btn btn-outline-success btn-block">Make Payment</button>
            <small class="form-text text-muted">By clicking "Make Payment" you are agreeing to the terms below.</small>
          </div>
        </div>
      </form>
<?PHP endif; ?>
<?PHP endif; ?>
    </div>
  </div>
<?PHP if(!$payment["paid"]): ?>
  <div class="card card-main">
    <div class="card-body text-muted"><small>Unless otherwise stated, all prices are in New Zealand Dollars. All payments are considered final, and refunds may not be given. Contact <a href="<?=$config["rep"]["email"];?>"><?=$config["rep"]["email"];?></a> if you have a discrepancy.</small></div>
  </div>
<?PHP endif; ?>

  <div class="card card-main d-print-none">
    <div class="card-body text-right">
      <a href="/payments?id=<?=$payment["id"];?>&print" class="btn btn-outline-secondary">Print</a>
    </div>
  </div>
<?PHP endif; if($view_payment === "error"): ?>
  <div class="alert alert-warning alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong>Oops!</strong> The requested payment does not exist, or you do not have permission to view it.
  </div>
<?PHP endif; if($view_payment === false || $view_payment === "error"): ?>
  <div class="card card-main">
    <div class="card-header">Payments</div>
<?=$app->tmpl("modules/payments", array("payments" => $payments["items"]));?>
  </div>
<?PHP endif; ?>

<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
