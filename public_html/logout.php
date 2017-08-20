<?php
define("PAGE_ID", "logout");
require("../server_private/include.php");

unset($_SESSION["user_id"]);
unset($_SESSION["user_as"]);

header("location: /login?msg=loggedout");

$app->end();
?>
