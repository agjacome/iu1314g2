<div class="center_content">
    <div class="center_title_bar"><?php print $lang ["user"]["list"]; ?></div>
    <ul class="list">
<?php for ($i = 0; $i < count($list); $i++) { ?>
        <li class="<?php if (($i + 1) % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=user&action=get&login=<?php print $list[$i]->getLogin(); ?>">
                <?php print $list[$i]->getLogin() . " - " . $list[$i]->email; ?>
            </a>
        </li>
<?php } ?>
    </ul>
</div> <!-- end of center content -->
