    <nav class="navbar fixed-top navbar-light">
      <a class="navbar-brand" href="/">
        <img src="/img/logo.png" class="d-inline-block align-top" alt="">
      </a>

<?PHP if($me !== false): ?>
      <form class="form-inline my-2 my-lg-0" action="/logout" method="POST">
        <span class="text-muted"><?=$me->info["name"];?></span>
<?PHP if($me->as): ?>
        &nbsp;&nbsp;&nbsp;
        <button class="btn btn-outline-danger btn-sm my-2 my-sm-0" type="submit" name="action" value="back">Back</button>
<?PHP endif; ?>
        &nbsp;&nbsp;&nbsp;
        <button class="btn btn-outline-danger btn-sm my-2 my-sm-0" type="submit" name="action" value="logout">Log Out</button>
      </form>
<?PHP endif; ?>
    </nav>
