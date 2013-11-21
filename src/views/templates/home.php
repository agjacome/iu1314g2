<div class="center_content">
    <div class="center_title_bar">Latest Sales</div>
    <ul class="list">
<?php
    $sales = array_reverse($sales);
    $count = count($sales) < 4 ? count($sales) : 4;
    for ($i = 0; $i < $count; $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=product&action=get&id=<?php print $sales[$i]->getId(); ?>"><?php print $sales[$i]->name; ?></a>
        </li>
<?php } ?>
    </ul>
    <div class="center_title_bar">Latest Biddings</div>
    <ul class="list">
<?php
    $biddings = array_reverse($biddings);
    $count = count($biddings) < 4 ? count($biddings) : 4;
    for ($i = 0; $i < $count; $i++) {
?>
        <li class="<?php if ($i % 2 == 0) print "even"; else print "odd"; ?>">
            <a href="/index.php?controller=product&action=get&id=<?php print $biddings[$i]->getId(); ?>"><?php print $biddings[$i]->name; ?></a>
        </li>
<?php } ?>
    </ul>
</div>
