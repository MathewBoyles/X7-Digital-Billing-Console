<?php
  define("PAGE_ID", "index");
  define("PAGE_TITLE", "Billing");
  define("ACTIVE_NAV", "index");
  require("../server_private/include.php");

  $app->login_required();

  $payments = $me->payments();
  $subscriptions = $me->subscriptions(array("status" => "active"));

  $app->tmpl("top"); ?>

<div class="card card-main">
  <div class="card-header">Account</div>
  <div class="card-body">
    <h4 class="card-title"><?=$me->info["name"];?></h4>
    <p class="card-text"><?=$me->info["email"];?></p>
  </div>
</div>

<div class="card card-main">
  <div class="card-header card-more">Pending Payments <a class="btn btn-outline-dark btn-sm pull-right" href="/payments">View All</a></div>
<?=$app->tmpl("modules/payments", array("payments" => $payments["items"], "pending" => true));?>
</div>

<div class="card card-main">
  <div class="card-header card-more">Active Subscriptions <a class="btn btn-outline-dark btn-sm pull-right" href="/subscriptions">View All</a></div>
<?=$app->tmpl("modules/subscriptions", array("subscriptions" => $subscriptions));?>
</div>

<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
