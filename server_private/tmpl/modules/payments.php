  <table class="table">
    <thead>
      <tr>
        <th>Description</th>
        <th>Due by</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
<?PHP foreach($data["payments"] as $item): ?>
<?PHP if(!$item["paid"] || !isset($data["pending"])): ?>
      <tr data-href="/payments?id=<?=$item["id"];?>">
        <td><?=$item["title"];?> (#PEND-<?=$item["id"];?>)</td>
        <td>
          <?=date('m/d/Y', $item["due"]);?>
<?PHP if($item["paid"]) { ?>
          <span class="badge badge-success">PAID</span>
<?PHP }else if($item["date"] > time()){ ?>
          <span class="badge badge-light">UPCOMING</span>
<?PHP }else if($item["due"] < time()){ ?>
          <span class="badge badge-danger">OVERDUE</span>
<?PHP }else{ ?>
          <span class="badge badge-secondary">DUE</span>
<?PHP } ?>
        </td>
        <td>$<?=number_format($item["amount"]/100, 2);?></td>
      </tr>
<?PHP endif; ?>
<?PHP endforeach; ?>
    </tbody>
  </table>
