<div class="center_content">
    <div class="center_title_bar"><?php print $lang ["bidding"]["list"]; ?></div>

    <ul class="list">
<?php
    $list = array_reverse($list);
    for ($i = 0; $i < count($list); $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=bidding&action=get&id=<?php print $list[$i]["bidding"]->getId(); ?>"><?php print $list[$i]["product"]->name; ?></a>
        </li>
<?php } ?>
    </ul>
</div>
