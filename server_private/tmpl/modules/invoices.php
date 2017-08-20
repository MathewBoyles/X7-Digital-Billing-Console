  <table class="table">
    <tbody>
<?PHP foreach($data["invoices"] as $item):
?>
      <tr data-href="/invoices?id=<?=$item["id"];?>">
        <td>$<?=number_format($item["amount_due"]/100, 2);?></td>
        <td class="text-right"><?=date('m/d/Y', $item["date"]);?><?PHP if($item["paid"]): ?> <span class="badge badge-pill badge-primary badge-line">PAID</span><?PHP endif; ?></td>
      </tr>
<?PHP endforeach; ?>
    </tbody>
  </table>
