<?php
  define("PAGE_ID", "admin");
  define("PAGE_TITLE", "Admin");
  define("ACTIVE_NAV", "admin");
  require("../server_private/include.php");

  $app->login_required();
  if($me->info["account_type"] !== "admin"):
    header("location: /");
    exit;
  endif;

  $users = array();

  if($users_query = $link->query("SELECT * FROM `".$config["mysql"]["prefix"]."users`")):
    while($user = mysqli_fetch_array($users_query, MYSQLI_ASSOC)):
      array_push($users, $user);
    endwhile;
    $users_query->close();
  endif;

  if(isset($_GET["action"])):
    foreach($users as $user):
      if($_GET["id"] == $user["id"]):

        if($_GET["action"] == "login" && $user["account_ready"] && $user["id"] !== $me->info["id"]):
          $_SESSION["user_as"] = $user["id"];
        	header("location: /");
          exit;
        endif;

        if($_GET["action"] == "suspend" && $user["id"] !== $me->info["id"] && $user["has_access"]):
          $link->query("UPDATE `".$config["mysql"]["prefix"]."users` SET `has_access` = '0' WHERE `id` = '".$user["id"]."'");
          header("location: /admin?msg=suspended");
          exit;
        endif;

        if($_GET["action"] == "enable" && !$user["has_access"]):
          $link->query("UPDATE `".$config["mysql"]["prefix"]."users` SET `has_access` = '1' WHERE `id` = '".$user["id"]."'");
          header("location: /admin?msg=enabled");
          exit;
        endif;

      endif;
    endforeach;
  endif;

  $messages = array(
    "suspended" => array(
      "success",
      "Account access suspended."
    ),
    "enabled" => array(
      "success",
      "Account access enabled."
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

  <div class="card card-main">
    <div class="card-header card-more">Users <a class="btn btn-outline-dark btn-sm pull-right" href="/admin?action=edit&id=new">Add User</a></div>
    <table class="table">
      <tbody>
<?PHP foreach($users as $user): ?>
        <tr>
          <td>
            <?=$user["name"];?>
<?PHP if($user["account_type"] == "admin"): ?>
            <i class="fa fa-shield text-success" aria-hidden="true"></i>
<?PHP endif; if(!$user["account_ready"]): ?>
            <span class="badge badge-pill badge-danger">Unactivated</span>
<?PHP endif; if(!$user["has_access"]): ?>
            <span class="badge badge-pill badge-danger">No access</span>
<?PHP endif; ?>

            <div class="dropdown show pull-right">
              <a class="badge badge-pill badge-primary badge-line dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                More...
              </a>

              <div class="dropdown-menu dropdown-menu-right">
<?PHP if($user["account_ready"] && $user["id"] !== $me->info["id"]): ?>
                <a class="dropdown-item" href="/admin?action=login&id=<?=$user["id"];?>">Login as</a>
<?PHP endif; ?>
                <a class="dropdown-item" href="/admin?action=email&id=<?=$user["id"];?>">Send email</a>

                <h6 class="dropdown-header">Billing</h6>
                <a class="dropdown-item" href="/admin?action=request&id=<?=$user["id"];?>">Request Payment</a>

                <h6 class="dropdown-header">Administrative</h6>
<?PHP if($user["id"] !== $me->info["id"] || !$user["has_access"]): ?>
                <a class="dropdown-item" href="/admin?action=<?=$user["has_access"]?"suspend":"enable";?>&id=<?=$user["id"];?>"><?=$user["has_access"]?"Suspend":"Enable";?> access</a>
<?PHP endif; ?>
                <a class="dropdown-item" href="/admin?action=edit&id=<?=$user["id"];?>">Edit</a>
              </div>
            </div>
          </td>
        </tr>
<?PHP endforeach; ?>
      </tbody>
    </table>

  </div>
<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
