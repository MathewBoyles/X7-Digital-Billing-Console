<?php
  define("PAGE_ID", "invoices");
  define("PAGE_TITLE", "Invoices");
  define("ACTIVE_NAV", "invoices");
  require("../server_private/include.php");

  $app->login_required();

  $invoices = $me->invoices();

  $view_invoice = false;
  if(isset($_GET["id"])):
    if($_GET["id"]):
      $view_invoice = "error";
      foreach($invoices as $item):
        if($item["id"] == $_GET["id"]):
          $view_invoice = true;
          $invoice = $item;

          if(isset($_GET["print"])):
            $app->tmpl("modules/invoice", $invoice);
            die;
          endif;

          break;
        endif;
      endforeach;
    endif;
  endif;

  $app->tmpl("top"); ?>

<?PHP if($view_invoice === true) : ?>
  <div class="card card-main">
    <div class="card-header card-more">Invoice (#<?=$invoice["id"];?>) <span class="btn btn-<?=$invoice["paid"]?"success":"danger";?> btn-sm pull-right"><?=$invoice["paid"]?"PAID":"UNPAID";?></span></div>
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
    <div class="card-header">Invoice Details</div>
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div>
            <span class="text-muted">ID:</span> <?=$invoice["id"];?>
          </div>
          <div>
            <span class="text-muted">Subscription:</span> <a href="/subscriptions?id=<?=$invoice["subscription"];?>"><?=$invoice["subscription"];?></a>
          </div>
        </div>
        <div class="col-sm-6">
          <div>
            <span class="text-muted">Date:</span> <?=date('m/d/Y', $invoice["date"]);?>
          </div>
          <div>
            <span class="text-muted">Period:</span> <?=date('m/d/Y', $invoice["period_start"]);?> to <?=date('m/d/Y', $invoice["period_end"]);?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-main">
    <table class="table">
      <tbody>
        <tr>
          <td class="text-right text-muted">Subtotal: <strong>$<?=number_format($invoice["subtotal"]/100, 2);?></strong></td>
        </tr>
<?PHP if($invoice["discount"]): ?>
        <tr>
          <td class="text-right text-muted">Discount: <strong>-$<?=
            number_format(
              ($invoice["discount"]["coupon"]["percent_off"]?(($invoice["subtotal"]/100)*$invoice["discount"]["coupon"]["percent_off"]):$invoice["discount"]["coupon"]["amount_off"])/100,
            2);
          ?></strong><br /><small><?=$invoice["discount"]["coupon"]["id"];?> — <?PHP
            if($invoice["discount"]["coupon"]["percent_off"]) echo $invoice["discount"]["coupon"]["percent_off"] . "%";
            else echo "$" . number_format($invoice["discount"]["coupon"]["amount_off"]/100, 2);
          ?> off</small></td>
        </tr>
<?PHP endif; ?>
        <tr>
          <td class="text-right text-muted">Total: <strong>$<?=number_format($invoice["total"]/100, 2);?></strong></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="card card-main d-print-none">
    <div class="card-body text-right">
      <a href="/invoices?id=<?=$invoice["id"];?>&print" class="btn btn-outline-secondary">Print</a>
    </div>
  </div>
<?PHP endif; if($view_invoice === "error"): ?>
  <div class="alert alert-warning alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong>Oops!</strong> The requested invoice does not exist, or you do not have permission to view it.
  </div>
<?PHP endif; if($view_invoice === false || $view_invoice === "error"): ?>
  <div class="card card-main">
    <div class="card-header card-more">Invoices</div>
<?=$app->tmpl("modules/invoices", array("invoices" => $invoices));?>
  </div>
<?PHP endif; ?>
<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
