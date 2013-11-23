<div class="center_content">
    <div class="center_title_bar"><?php print $lang["product"]["details"]; ?></div>

    <ul class="list">
        <li class="even"><?php print "<strong>" . $lang["product"]["name"]    . ":</strong> " . $product->name; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["owner"]   . ":</strong> " . $product->getOwner(); ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["state"]   . ":</strong> " . $product->state; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["descr"]   . ":</strong> " . $product->description; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["rateAvg"] . ":</strong> " . $rateAvg; ?> / 5</li>
        <li class="odd">
<?php if ($this->isLoggedIn()) { ?>
            <a href="/index.php?controller=product&action=rate&prod=<?php print $product->getId(); ?>">
                <?php print $lang["product"]["rate"]; ?>
            </a>
<?php   if ($this->session->username === $product->getOwner() || $this->isAdmin()) { ?>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=product&action=update&id=<?php print $product->getId(); ?>">
                <?php print $lang["product"]["update"]; ?>
            </a>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=product&action=delete&id=<?php print $product->getId(); ?>" onclick="deleteproductt();">
                <?php print $lang["product"]["delete"]; ?>
            </a>
<?php        if ($product->state === "pendiente") { ?>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=sale&action=create&product=<?php print $product->getId(); ?>">
                <?php print $lang["product"]["sale"]; ?>
            <a>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=bidding&action=create&product=<?php print $product->getId(); ?>">
                <?php print $lang["product"]["auction"]; ?>
            </a>
<?php } } } ?>
        </li>
    </ul>

    <div class="center_title_bar"><?php print $lang["product"]["ratings"]; ?></div>
    <ul class="list">
<?php

    if (count($ratings) > 0) {
        foreach ($ratings as $rating) {
?>
        <li class="even">
            <?php print "<strong>" . $lang["product"]["author"]  . ":</strong> " . $rating->getLogin(); ?>
            <br />
            <?php print "<strong>" . $lang["product"]["rating"]  . ":</strong> " . $rating->rating; ?>
            <br />
            <?php print "<strong>" . $lang["product"]["comment"] . ":</strong> " . $rating->commentary; ?>
        </li>
<?php
        }
    } else {
?>
        <li class="even">
            <?php print $lang["product"]["no_ratings"]; ?>
        </li>
<?php } ?>
    </ul>

</div> <!-- end of center content -->
