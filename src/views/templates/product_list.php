<div class="center_content">
    <div class="center_title_bar"><?php print $lang["product"]["list"]; ?></div>
    <ul class="list">
<?php
    for ($i = 0; $i < count($list); $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=product&action=get&id=<?php print $list[$i]->getId(); ?>"><?php print $list[$i]->name; ?></a>
            &nbsp;|&nbsp;
            <strong><?php print $lang["product"]["state"]; ?>:</strong> <?php print $list[$i]->state; ?>
        </li>
<?php } ?>
    </ul>
</div>
