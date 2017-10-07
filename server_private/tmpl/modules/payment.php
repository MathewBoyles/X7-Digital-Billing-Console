<?PHP
  $payment = $data;
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment | X7 Digital</title>

  <style>
    .invoice-box {
      max-width: 800px;
      margin: auto;
      padding: 30px;
      font-size: 16px;
      line-height: 24px;
      font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
      color: #555;
    }

    .invoice-box p {
      margin: 0;
    }

    .invoice-box table {
      width: 100%;
      line-height: inherit;
      text-align: left;
    }

    .invoice-box table td {
      padding: 5px;
      vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
      text-align: right;
    }

    .invoice-box table tr.top table td {
      padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
      font-size: 45px;
      line-height: 45px;
      color: #333;
    }

    .invoice-box table tr.information table td {
      padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
      background: #eee;
      border-bottom: 1px solid #ddd;
      font-weight: bold;
    }

    .invoice-box table tr.details td {
      padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
      border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
      border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
      border-top: 2px solid #eee;
      font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
      .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
      }

      .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
      <tr class="top">
        <td colspan="2">
          <table>
            <tr>
              <td class="title">
                <img src="/img/logo.png" style="width:100%; max-width:300px;">
              </td>

              <td>
                Invoice #PEND-<?=$payment["id"];?><br><?=date('m/d/Y', $payment["date"]);?><br>Due: <?=date('m/d/Y', $payment["due"]);?>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr class="information">
        <td colspan="2">
          <table>
            <tr>
              <td>
                <strong>X7 Digital</strong><br><?=$config["rep"]["name"];?><br><?=$config["rep"]["email"];?>
              </td>

              <td>
                <?=$me->info["name"];?><br><?=$me->info["email"];?>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr class="heading">
        <td>
          Payment Info
        </td>

        <td>
        </td>
      </tr>

      <tr class="info">
        <td>
          <?=$payment["paid"]?("Paid ".date('m/d/Y', $payment["paid"])):"Unpaid";?>
        </td>

        <td>
        </td>
      </tr>

      <tr class="heading">
        <td>
          Item
        </td>

        <td>
          Price
        </td>
      </tr>

<?PHP
  $payment_lines = explode(";", $payment["description"]);
  foreach($payment_lines as $line):
    $line_info = explode("==", $line);
?>
        <tr class="item">
          <td><?=$line_info[0];?></td>
          <td><?PHP if(isset($line_info[1])): echo "$" . ($line_info[1]<0?"(":"") . number_format(($line_info[1]<0?(-$line_info[1]):$line_info[1])/100, 2) . ($line_info[1]<0?")":""); else:echo "&nbsp;";endif; ?></td>
        </tr>
<?PHP endforeach; ?>

      <tr class="total">
        <td></td>

        <td>
          Total: $<?=number_format($payment["amount"]/100, 2);?>
        </td>
      </tr>
    </table>
  </div>
</body>
<script>window.print();</script>
</html>
