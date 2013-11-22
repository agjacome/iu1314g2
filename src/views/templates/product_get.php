<div class="center_content">
    <div class="center_title_bar"><?php print $lang["product"]["details"]; ?></div>

    <ul class="list">
        <li class="even"><?php print "<strong>" . $lang["product"]["name"]    . ":</strong> " . $product->name; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["owner"]   . ":</strong> " . $product->getOwner(); ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["state"]   . ":</strong> " . $product->state; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["descr"]   . ":</strong> " . $product->description; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["rateAvg"] . ":</strong> " . $rateAvg; ?></li>
        <li class="odd">
<?php if ($product->state === "venta") { ?>
            <a href="/index.php?controller=sale&action=purchase&id=<?php print $product->getId(); ?>"><?php print $lang["product"]["buy"]; ?></a>
<?php } elseif ($produc->state === "subasta") { ?>
            <a href="/index.php?controller=bidding&action=bid&id=<?php print $product->getId(); ?>"><?php print $lang["product"]["buy"]; ?></a>
<?php } ?>
        </li>
    </ul>

</div> <!-- end of center content -->
