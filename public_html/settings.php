<?php
  define("PAGE_ID", "settings");
  define("PAGE_TITLE", "Settings");
  define("ACTIVE_NAV", "settings");
  require("../server_private/include.php");

  $app->login_required();

  $cards = $me->cards();
  $stripe = $me->stripe();

  $view_card = false;
  if(isset($_GET["view"]) && isset($_GET["id"])):
    if($_GET["view"] == "card"):
      $view_card = "error";
      foreach($cards as $item):
        if($item["id"] == $_GET["id"]):
          $view_card = true;
          $card = $item;

          if(isset($_POST["action"]) && $item["id"] != $stripe["default_source"]):
            if($_POST["action"] == "primary"):
              $stripe->default_source = $item["id"];
              $stripe->save();
              header("location: /settings?msg=cards.saved");
            endif;
            if($_POST["action"] == "remove"):
              $item->delete();
              header("location: /settings?msg=cards.removed");
            endif;
          endif;
          break;
        endif;
      endforeach;
    endif;
  endif;

  $messages = array(
    "password" => array(
      "success",
      "Your password has been changed."
    ),
    "card" => array(
      "success",
      "Your card has been updated."
    ),
    "cards.max" => array(
      "warning",
      "You may not have more than 5 cards connected to your account at a time."
    ),
    "cards.saved" => array(
      "success",
      "Your default card has been updated."
    ),
    "cards.removed" => array(
      "success",
      "Card removed."
    )
  );

  $app->tmpl("top"); ?>
<?PHP if(isset($_GET["msg"]) && isset($messages[$_GET["msg"]])): ?>
  <div class="alert alert-<?=$messages[$_GET["msg"]][0];?> alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <?=$messages[$_GET["msg"]][1];?>
  </div>
<?PHP endif; ?>

<?PHP if($view_card === true): ?>

  <div class="card card-main">
    <div class="card-header card-more">Card Details</div>
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div>
            <span class="text-muted">ID:</span> <?=$card["id"];?>
          </div>
          <div>
            <span class="text-muted">Name:</span> <?=$card["name"];?>
          </div>
          <div>
            <span class="text-muted">Type:</span> <?=$card["brand"];?> <?=$card["funding"];?>
          </div>
        </div>
        <div class="col-sm-6">
          <div>
            <span class="text-muted">Number:</span> ••••<?=$card["last4"];?>
          </div>
          <div>
            <span class="text-muted">Expiry:</span> <?=$card["exp_month"];?> / <?=$card["exp_year"];?>
          </div>
          <div>
            <span class="text-muted">Origin:</span> <?=$card["country"];?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-main d-print-none">
    <div class="card-body text-right">
<?PHP if($card["id"] == $stripe["default_source"]): ?>
      <em class="text-muted">This is your default card. You must change your default before you may remove this card.</em>
<?PHP else: ?>
      <form class="pull-right" method="POST">
        &nbsp;
        <input type="hidden" name="action" value="remove" />
        <button type="submit" class="btn btn-outline-danger">Remove Card</button>
      </form>
      <form class="pull-right" method="POST">
        <input type="hidden" name="action" value="primary" />
        <button type="submit" class="btn btn-outline-primary">Make Primary</button>
      </form>
<?PHP endif; ?>
    </div>
  </div>

  <div class="card card-main d-print-none">
    <div class="card-body text-sm text-center text-muted">Your default card will be used for all future subscription invoices. You may select which card to use when making manual payments.</div>
  </div>
<?PHP endif; if($view_card === "error"): ?>
  <div class="alert alert-warning alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong>Oops!</strong> The requested card does not exist, or you do not have permission to view it.
  </div>
<?PHP endif; if($view_card === false || $view_card === "error"): ?>
  <div class="card card-main">
    <div class="card-header card-more">Password <a class="btn btn-outline-dark btn-sm pull-right" href="/password">Change Password</a></div>
    <div class="card-body">
      <span class="text-muted">Last updated:</span> <?=date('m/d/Y', $me->info["password_timestamp"]);?>
    </div>
  </div>

  <div class="card card-main">
    <div class="card-header card-more">Cards <a class="btn btn-outline-dark btn-sm pull-right" href="/card">Add Card</a></div>
    <table class="table">
      <tbody>
<?PHP foreach($cards as $card): ?>
        <tr data-href="/settings?view=card&id=<?=$card["id"];?>">
          <td><?=$card["name"].($card["name"]?" — ":"");?> <?=$card["brand"];?> ••••<?=$card["last4"];?><?PHP
          if($stripe["default_source"] == $card["id"]):
          ?><span class="badge badge-pill badge-primary badge-line">Default</span><?PHP endif; ?></td>
        </tr>
<?PHP endforeach; ?>
      </tbody>
    </table>

  </div>
<?PHP endif; ?>
<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
