<div class="center_content">
    <div class="center_title_bar">Latest Products</div>
    <ul>
<?php foreach($sales as $sale) { ?>
        <li>
            <?php print $sale->name; ?> :
            <a href="/index.php?controller=product&action=get&id=<?php print $sale->getId(); ?>">Detalles</a>
        </li>
<?php } ?>
    </ul>
    <div class="center_title_bar">Recommended Products</div>
    <ul>
<?php foreach($biddings as $bidding) { ?>
        <li>
            <?php print $bidding->name; ?> :
            <a href="/index.php?controller=product&action=get&id=<?php print $bidding->getId(); ?>">Detalles</a>
        </li>
<?php } ?>
    </ul>
</div> <!-- end of center content -->
