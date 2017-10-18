<?php
  define("PAGE_ID", "subscriptions");
  define("PAGE_TITLE", "Subscriptions");
  define("ACTIVE_NAV", "subscriptions");
  require("../server_private/include.php");

  $app->login_required();

  $subscriptions = $me->subscriptions();

  $view_subscription = false;
  if(isset($_GET["id"])):
    if($_GET["id"]):
      $view_subscription = "error";
      foreach($subscriptions as $item):
        if($item["id"] == $_GET["id"]):
          $view_subscription = true;
          $subscription = $item;
          $invoices = $me->invoices(array(
            "subscription" => $item["id"]
          ));

          $total_price = 0;
          $discounted = 0;
          foreach($item["items"]["data"] as $line_item):
            $total_price += ($line_item["plan"]["amount"] * $line_item["quantity"]);
          endforeach;
          $original_price = $total_price;

          if($item["discount"]):
            if($item["discount"]["coupon"]["valid"]):
              $discounted += $item["discount"]["coupon"]["amount_off"];
              $discounted += ($total_price/100) * $item["discount"]["coupon"]["percent_off"];
            endif;
          endif;

          $total_price -= $discounted;
          if($total_price < 0) $total_price = 0;

          break;
        endif;
      endforeach;
    endif;
  endif;

  $app->tmpl("top"); ?>

<?PHP if($view_subscription === true): ?>
  <div class="d-print-none back-top">
    <a href="/subscriptions"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back to subscriptions</a>
  </div>

  <div class="card card-main">
    <div class="card-header">Subscription Details</div>
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div>
            <span class="text-muted">ID:</span> <?=$subscription["id"];?>
          </div>
          <div>
            <span class="text-muted">Created:</span> <?=date('m/d/Y', $subscription["created"]);?>
          </div>
        </div>
        <div class="col-sm-6">
          <div>
            <span class="text-muted">Current period:</span> <?=date('m/d/Y', $subscription["current_period_start"]);?> to <?=date('m/d/Y', $subscription["current_period_end"]);?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-main">
    <div class="card-header">Plans</div>
    <table class="table table-fixed">
      <tbody>
<?PHP foreach($subscription["items"]["data"] as $item): ?>
        <tr class="text-muted">
          <td class="text-dark"><strong><?=$item["plan"]["name"];?></strong></td>
          <td>$<?=number_format($item["plan"]["amount"]/100, 2);?>/<?=$item["plan"]["interval"];?></td>
          <td>Quantity: <?=$item["quantity"];?></td>
          <td>$<?=number_format(($item["plan"]["amount"]*$item["quantity"])/100, 2);?>/<?=$item["plan"]["interval"];?></td>
        </tr>
<?PHP endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card card-main">
    <div class="card-header">Invoices</div>
    <table class="table">
      <tbody>
<?PHP foreach($invoices as $item): ?>
        <tr data-href="/invoices?id=<?=$item["id"];?>">
          <td>$<?=number_format($item["amount_due"]/100, 2);?></td>
          <td class="text-right"><?=date('m/d/Y', $item["date"]);?><?PHP if($item["paid"]): ?> <span class="badge badge-pill badge-primary badge-line">PAID</span><?PHP endif; ?></td>
        </tr>
<?PHP endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card card-main">
    <div class="card-header">Discount</div>
    <div class="card-body">
<?PHP if($subscription["discount"]): ?>
      <span class="text-muted">Coupon:</span>
      <span><?=$subscription["discount"]["coupon"]["id"];?> — <?PHP
        if($subscription["discount"]["coupon"]["percent_off"]) echo $subscription["discount"]["coupon"]["percent_off"] . "%";
        else echo "$" . number_format($subscription["discount"]["coupon"]["amount_off"]/100, 2);
        echo " off ";
        if($subscription["discount"]["coupon"]["duration"] == "repeating") echo "for " . $subscription["discount"]["coupon"]["duration_in_months"] . " months";
        else echo $subscription["discount"]["coupon"]["duration"];
      ?></span>
<?PHP else: ?>
      <div class="text-center text-muted">
        <em>No active coupon</em>
      </div>
<?PHP endif; ?>
    </div>
  </div>

  <div class="card card-main d-print-none">
    <div class="card-body text-right">
      <button type="button" class="btn btn-outline-secondary" onclick="window.print()">Print</button>
    </div>
  </div>
<?PHP endif; if($view_subscription === "error"): ?>
  <div class="alert alert-warning alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong>Oops!</strong> The requested subscription does not exist, or you do not have permission to view it.
  </div>
<?PHP endif; if($view_subscription === false || $view_subscription === "error"): ?>
  <div class="card card-main">
    <div class="card-header">Subscriptions</div>
  <?=$app->tmpl("modules/subscriptions", array("subscriptions" => $subscriptions));?>
  </div>
<?PHP endif; ?>
<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
