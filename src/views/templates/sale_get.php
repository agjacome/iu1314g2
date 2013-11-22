<div class="center_content">
    <div class="center_title_bar"><?php print $lang["sale"]["details"]; ?></div>

    <ul class="list">
        <li class="even"><?php print "<strong>" . $lang["product"]["name"]    . ":</strong> " . $product->name; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["owner"]   . ":</strong> " . $product->getOwner(); ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["descr"]   . ":</strong> " . $product->description; ?></li>
        <li class="even"><?php print "<strong>" . $lang["product"]["rateAvg"] . ":</strong> " . $rate; ?> / 5</li>
        <li class="even"><?php print "<strong>" . $lang["sale"]["stock"]      . ":</strong> " . $sale->stock; ?></li>
        <li class="even"><?php print "<strong>" . $lang["sale"]["price"]      . ":</strong> " . $sale->price; ?>€</li>
        <li class="odd">
<?php if ($this->isLoggedIn()) { ?>
            <a href="/index.php?controller=sale&action=purchase&id=<?php print $sale->getId(); ?>">
                Comprar
            </a>
            &nbsp;|&nbsp;
            Comentar
<?php   if ($this->session->username === $product->getOwner() || $this->isAdmin()) { ?>
            | Modificar | Eliminar
<?php
        }
      } else {
?>
            <a href="/index.php?controller=user&action=login">
                <?php print $lang["sale"]["login_to_buy"]; ?>
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
            <?php print "<strong>" . $lang["user"]["username"] . ":</strong> "; ?>
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
