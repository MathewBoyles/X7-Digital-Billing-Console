  <table class="table">
    <tbody>
<?PHP foreach($data["subscriptions"] as $item):
$total_price = 0;
$discounted = 0;
foreach($item["items"]["data"] as $line_item):
  $total_price += ($line_item["plan"]["amount"] * $line_item["quantity"]);
endforeach;
$original_price = $total_price;

if($item["discount"]) {
  if($item["discount"]["coupon"]["valid"]) {
    $discounted += $item["discount"]["coupon"]["amount_off"];
    $discounted += ($total_price/100) * $item["discount"]["coupon"]["percent_off"];
  }
}

$total_price -= $discounted;
if($total_price < 0) $total_price = 0;

$item_description = $item_description = $item["items"]["data"][0]["plan"]["name"];
if(count($item["items"]["data"]) > 1) $item_description .= " and " . (count($item["items"]["data"]) -1) ." more...";
if(isset($item["metadata"]["description"])) $item_description = $item["metadata"]["description"];
?>
      <tr data-href="/subscriptions?id=<?=$item["id"];?>">
        <td><?=$item_description;?> ($<?=number_format($original_price/100, 2);?>/<?=$item["items"]["data"][0]["plan"]["interval"];?>) <span class="badge badge-pill badge-primary badge-line"><?=isset($item["metadata"]["status"])?$item["metadata"]["status"]:$item["status"];?></span></td>
      </tr>
<?PHP endforeach; ?>
    </tbody>
  </table>
