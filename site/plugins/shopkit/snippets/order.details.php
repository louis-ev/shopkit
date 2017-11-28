<!-- This snippet contains inline styles because it will be embedded into PDFs and emails -->
<?php $site = site(); ?>
<h1><?= _t('order-details') ?></h1>

<p><strong><?= _t('transaction-id') ?>:</strong> <?= $txn->txn_id() ?></p>

<p>
  <?= $txn->payer_firstname() ?> <?= $txn->payer_lastname() ?> (<?= $txn->payer_email() ?>)
  <?php if ($site->mailing_address()->bool()) { ?>
    <br>
    <?= $txn->shipping_address()->toStructure()->address1() ?><br>
    <?= $txn->shipping_address()->toStructure()->address2()->isNotEmpty() ? $txn->shipping_address()->toStructure()->address2().'<br>' : '' ?>
    <?= $txn->shipping_address()->toStructure()->city() ?>, <?= $txn->shipping_address()->toStructure()->state() ?><br>
    <?= $txn->shipping_address()->toStructure()->country() ?><br>
    <?= $txn->shipping_address()->toStructure()->postcode() ?>
  <?php } ?>
</p>

<div class="table-overflow">
    <table dir="auto" class="checkout" style="width: 100%;">
        <thead>
            <tr>
                <th style="text-align: left;"><?= _t('product') ?></th>
                <th style="text-align: right;"><?= _t('quantity') ?></th>
                <th style="text-align: right;"><?= _t('price') ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($txn->products()->toStructure() as $item) { ?>
                <tr>
                    <td>
                        <strong><?= $item->name() ?></strong><br>
                        <?php e($item->sku()->isNotEmpty(), '<strong>SKU</strong> '.$item->sku().' / ') ?>
                        <?php e($item->variant()->isNotEmpty(), $item->variant()) ?>
                        <?php e($item->option()->isNotEmpty(), ' / '.$item->option()) ?>
                    </td>
                    <td style="text-align: right;"><?= $item->quantity() ?></td>
                    <td style="text-align: right;">
                      <?php
                        if ($item->{'sale-amount'}->value === false) {
                            echo formatPrice(($item->amount()->value) * $item->quantity()->value);
                        } else {
                            echo '<del class="badge">'.formatPrice($item->amount()->value * $item->quantity()->value).'</del><br>';
                            echo formatPrice($item->{'sale-amount'}->value * $item->quantity()->value);
                        }
                      ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

        <tfoot>
            <tr>
                <td></td>
                <td style="text-align: right;"><?= _t('subtotal') ?></td>
                <td style="text-align: right;"><?= formatPrice($txn->subtotal()->value) ?></td>
            </tr>

            <tr>
                <td></td>
                <td style="text-align: right;">
                  <?= _t('shipping') ?><br>
                  <small>
                    <?= $txn->shipping_address()->toStructure()->country() ?> &ndash; <?= $txn->shippingmethod() ?>
                  </small>
                </td>
                <td style="text-align: right;"><?= formatPrice($txn->shipping()->value) ?></td>
            </tr>

            <?php foreach ($txn->taxes()->yaml() as $tax_rate => $tax_amt) { ?>
                <?php if ($tax_rate === 'total') continue; ?>
                <tr>
                    <td></td>
                    <td style="text-align: right;"><?= _t('tax') ?> <?= $tax_rate * 100 ?>%</td>
                    <td style="text-align: right;"><?= formatPrice($tax_amt) ?></td>
                </tr>
            <?php } ?>

            <?php if ($txn->discount()->value > 0) {  ?>
                <tr>
                    <td></td>
                    <td style="text-align: right;"><?= _t('discount') ?></td>
                    <td style="text-align: right;"><?= '&ndash; '.formatPrice($txn->discount()->value) ?></td>
                </tr>
            <?php } ?>

            <?php if ($txn->giftcertificate()->value > 0) { ?>
                <tr>
                    <td></td>
                    <td style="text-align: right;"><?= _t('gift-certificate') ?></td>
                    <td style="text-align: right;"><?= '&ndash; '.formatPrice($txn->giftcertificate()->value) ?></td>
                </tr>
            <?php } ?>

            <tr class="total">
                <td></td>
                <td style="text-align: right;"><?= _t('total') ?></td>
                <td style="text-align: right;">
                    <?php 
                      $total = $txn->subtotal()->value + $txn->shipping()->value - $txn->discount()->value - $txn->giftcertificate()->value;
                      if (!$site->tax_included()->bool()) $total = $total + $txn->tax()->value;
                    ?>
                    <?= $site->currency_code() ?>
                    <?= formatPrice($total) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>