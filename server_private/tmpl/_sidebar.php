        <nav class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="index"?" active":"";?>" href="/">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="downloads"?" active":"";?>" href="/downloads">Downloads</a>
            </li>
          </ul>

          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="payments"?" active":"";?>" href="/payments">Payments</a>
            </li>
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="subscriptions"?" active":"";?>" href="/subscriptions">Subscriptions</a>
            </li>
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="invoices"?" active":"";?>" href="/invoices">Invoices</a>
            </li>
          </ul>

          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="settings"?" active":"";?>" href="/settings">Settings</a>
            </li>
<?PHP if($me->info["account_type"] == "admin"): ?>
            <li class="nav-item">
              <a class="nav-link<?=ACTIVE_NAV=="admin"?" active":"";?>" href="/admin">Admin</a>
            </li>
<?PHP endif; ?>
          </ul>
        </nav>
