<div class="contact_form">
<div class="center_title_bar"><?php print $lang["sale"]["buy"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form method="post" action="index.php" name="bidProd" accept-charset="utf-8">
                    <input type="hidden" name="controller" value="bidding" />
                    <input type="hidden" name="action" value="makeBid" />
                    <input type="hidden" name="bidding" value="<?php print $bidding->getId(); ?>" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["bidding"]["quantity"]; ?></strong></label>
                        <input type="text" name="quantity" class="contact_input" value="<?php print $currentBid + 1; ?>" />
                    </div>

                    <input type="button" onclick="bidProdd()" value="<?php print $lang["general"]["accept"]; ?>">
                </form>
            </div>
        </div>
    <div class="bottom_prod_box_big"></div>
</div><!-- end of center content -->
