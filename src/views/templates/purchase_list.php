<div class="center_content">
    <div class="center_title_bar"><?php print $lang["sale"]["purchased"]; ?></div>
    <ul class="list">
<?php
    for ($i = 0; $i < count($list); $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <strong><?php print $lang["product"]["name"]; ?>:</strong> <a href="/index.php?controller=product&action=get&id=<?php print $list[$i]["product"]->getId(); ?>"><?php print $list[$i]["product"]->name; ?></a>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=sale&action=get&id=<?php print $list[$i]["sale"]->getId(); ?>"><?php print $lang["sale"]["details"]; ?></a>
            <br />
            <strong><?php print $lang["sale"]["stock"]; ?>:</strong> <?php print $list[$i]["purchase"]->quantity; ?>
            &nbsp;|&nbsp;
            <strong><?php print $lang["sale"]["date"]; ?>:</strong> <?php print $list[$i]["purchase"]->date; ?>
            <br />
            <strong><?php print $lang["sale"]["payMethod"]; ?>: </strong>
            <?php if ($list[$i]["payment"]->payMethod === "tarjeta") print $lang["sale"]["creditCard"]; else print "PayPal"; ?>
            &nbsp;-&nbsp;
            <?php if ($list[$i]["payment"]->payMethod === "tarjeta") print $list[$i]["payment"]->creditCard; else print $list[$i]["payment"]->paypal; ?>
        </li>
<?php } ?>
    </ul>
</div>
