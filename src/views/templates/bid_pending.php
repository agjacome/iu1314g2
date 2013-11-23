<div class="center_content">
    <div class="center_title_bar"><?php print $lang["bidding"]["pending_pay"]; ?></div>
    <ul class="list">
<?php
    for ($i = 0; $i < count($list); $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <strong><?php print $lang["product"]["name"]; ?>:</strong> <a href="/index.php?controller=product&action=get&id=<?php print $list[$i]["product"]->getId(); ?>"><?php print $list[$i]["product"]->name; ?></a>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=bidding&action=get&id=<?php print $list[$i]["bidding"]->getId(); ?>"><?php print $lang["bidding"]["details"]; ?></a>
            <br />
            <strong><?php print $lang["bidding"]["quantity"]; ?>:</strong> <?php print $list[$i]["bid"]->quantity; ?>€
            &nbsp;|&nbsp;
            <strong><?php print $lang["bidding"]["date"]; ?>:</strong> <?php print $list[$i]["bid"]->date; ?>
            <br />
            <a href="/index.php?controller=bidding&action=payBid&bid=<?php print $list[$i]["bid"]->getId(); ?>"><?php print $lang["bidding"]["pay_now"]; ?></a>
        </li>
<?php } ?>
    </ul>
</div>
