<?php
  define("PAGE_ID", "downloads");
  define("PAGE_TITLE", "Downloads");
  define("ACTIVE_NAV", "downloads");
  require("../server_private/include.php");

  $app->login_required();

  $storage_dir = "../server_private/downloads/" . $me->info["uid"];

  $files = glob(rtrim($storage_dir, '/').'/*', GLOB_NOSORT);
  $total_usage = 0;
  foreach($files as $each) {
    if(isset($_GET["action"]) && isset($_GET["file"])){
      if($_GET["file"] == md5(basename($each))){
        if($_GET["action"] == "download") {
          header("Content-Type: ". mime_content_type($each));
          header("Content-Disposition: attachment; filename=\"" . basename($each) . "\"");
          echo file_get_contents($each);
          exit;
        }
        if($_GET["action"] == "delete") {
          unlink($each);
          header("location: /downloads");
          exit;
        }
      }
    }
    $total_usage += filesize($each);
  }

  $upload_return = false;
  if(isset($_POST["action"]) && isset($_FILES["upload"])){
    $target_dir = $storage_dir;
    $target_file = $target_dir . "/" . basename($_FILES["upload"]["name"]);
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    if (file_exists($target_file)) $upload_return = "File with the same name already exists.";
    else if($_FILES["upload"]["size"] > (15*1024*1024)) $upload_return = "Sorry, your file is too large (max 15MB).";
    else if(($_FILES["upload"]["size"] + $total_usage) > $me->downloads_limit) $upload_return = "You do not have enough storage space remaining for this file.";
    else if($_FILES["upload"]["error"] > 0) $upload_return = "An error occured while uploading your file (ERR:".$_FILES["upload"]["error"].").";
    if(!$upload_return){
        if(move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
          array_unshift($files, $target_file);
          $total_usage += $_FILES["upload"]["size"];
          $upload_return = "uploaded";
        }
        else $upload_return = "An error occured while uploading your file.";
    }
  }

  sort($files);

  $app->tmpl("top"); ?>
<?PHP if($upload_return && $upload_return !== "uploaded") { ?>
  <div class="alert alert-warning alert-dismissible fade show card-main" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <?=$upload_return;?>
  </div>
<?PHP } ?>

  <div class="card card-main">
    <div class="card-header">Downloads</div>
    <div class="card-body">
      <p class="card-text"><strong><?=round(($total_usage/$me->downloads_limit*100),2);?>% (<?=$app->format_size($total_usage);?>)</strong> used of <strong><?=$app->format_size($me->downloads_limit);?></strong> limit.</p>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>File</th>
          <th>Size</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
<?PHP foreach($files as $each) { ?>
        <tr>
          <td><a href="/downloads?action=download&file=<?=md5(basename($each));?>"><?=basename($each);?></a></td>
          <td><?=$app->format_size(filesize($each));?></td>
          <td><a href="/downloads?action=download&file=<?=md5(basename($each));?>">Download</a><br /><a href="/downloads?action=delete&file=<?=md5(basename($each));?>">Delete</a></td>
        </tr>
<?PHP } ?>
      </tbody>
    </table>
  </div>

  <div class="card card-main">
    <div class="card-header">Upload</div>
    <div class="card-body">
      <form action="/downloads" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload" />
        <div class="form-group">
          <label for="downloadsUpload">Upload new file</label>
          <input type="file" class="form-control-file" name="upload" id="downloadsUpload">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>

<?PHP
  $app->tmpl("bottom");
  $app->end();
?>
