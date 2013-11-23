<div class="center_content">
    <div class="center_title_bar"><?php print $lang["bidding"]["details"]; ?></div>

    <ul class="list">
        <li class="even"><?php print "<strong>" . $lang["product"]["name"]      . ":</strong> " . $product->name; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["owner"]     . ":</strong> " . $product->getOwner(); ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["descr"]     . ":</strong> " . $product->description; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["rateAvg"]   . ":</strong> " . $rate; ?> / 5</li>
        <li class="even"><?php print "<strong>" . $lang["bidding"]["minBid"]    . ":</strong> " . $bidding->minBid; ?></li>
        <li class="even"><?php print "<strong>" . $lang["bidding"]["currBid"]   . ":</strong> " . $currentBid; ?></li>
        <li class="even"><?php print "<strong>" . $lang["bidding"]["limitDate"] . ":</strong> " . date("d-m-Y", strtotime($bidding->limitDate)); ?></li>
        <li class="odd">
<?php if ($this->isLoggedIn()) {
        if (date("Y-m-d H:i:s") < $bidding->limitDate && $product->getOwner() !== $username) { ?>
            <a href="/index.php?controller=bidding&action=makeBid&bidding=<?php print $bidding->getId(); ?>">
                <?php print $lang["bidding"]["bid"]; ?>
            </a>
            &nbsp;|&nbsp;
<?php   } ?>
            <a href="/index.php?controller=product&action=rate&prod=<?php print $product->getId(); ?>">
                <?php print $lang["product"]["rate"]; ?>
            </a>
<?php   if ($this->isAdmin()) { ?>
            &nbsp;|&nbsp;
            <a href="/index.php?controller=bidding&action=delete&id=<?php print $bidding->getId(); ?>">
                <?php print $lang["bidding"]["delete"]; ?>
            </a>
<?php
        }
      } else {
?>
            <a href="/index.php?controller=user&action=login">
                <?php print $lang["bidding"]["login_to_bid"]; ?>
            </a>
<?php } ?>
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
