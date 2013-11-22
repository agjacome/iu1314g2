<div class="center_content">
<div class="center_title_bar"><?php print $lang["product"]["last_sale"]; ?></div>
    <ul class="list">
<?php
    $sales = array_reverse($sales);
    $count = count($sales) < 4 ? count($sales) : 4;
    for ($i = 0; $i < $count; $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=sale&action=get&id=<?php print $sales[$i]["sale"]->getId(); ?>"><?php print $sales[$i]["product"]->name; ?></a>
        </li>
<?php } ?>
    </ul>
    <div class="center_title_bar"><?php print $lang["product"]["last_bidding"]; ?></div>
    <ul class="list">
<?php
    $biddings = array_reverse($biddings);
    $count = count($biddings) < 4 ? count($biddings) : 4;
    for ($i = 0; $i < $count; $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=bidding&action=get&id=<?php print $biddings[$i]["bidding"]->getId(); ?>"><?php print $biddings[$i]["product"]->name; ?></a>
        </li>
<?php } ?>
    </ul>
</div>
