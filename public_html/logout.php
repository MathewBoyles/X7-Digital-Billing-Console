<?php
define("PAGE_ID", "logout");
require("../server_private/include.php");

unset($_SESSION["user_as"]);

if(!isset($_POST["action"])) unset($_SESSION["user_id"]);
else if($_POST["action"] == "logout") unset($_SESSION["user_id"]);

header("location: /login?msg=loggedout");

$app->end();
?>
