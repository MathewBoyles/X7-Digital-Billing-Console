<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style.css">
    <title><?=PAGE_TITLE;?> | X7 Digital</title>
  </head>
  <body data-pageid="<?=PAGE_ID;?>">
<?=$app->tmpl("_nav");?>

    <div class="container-fluid" id="page">
      <div class="row">

<?PHP if(ACTIVE_NAV === "DISABLED") { ?>
        <main class="col-sm-12 ml-sm-auto col-md-12 pt-3" role="main">
<?PHP }else { ?>
<?=$app->tmpl("_sidebar"); ?>

        <main class="col-sm-9 ml-sm-auto col-md-10 pt-3" role="main">
<?PHP } ?>
