<div class="contact_form">
<div class="center_title_bar"><?php print $lang["bidding"]["create"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form action="index.php" name="insertBidding()" method="post" accept-charset="utf-8">
                    <input type="hidden" name="controller" value="bidding" />
                    <input type="hidden" name="action" value="create" />
                    <input type="hidden" name="product" value="<?php print $product->getId(); ?>" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["bidding"]["minBid"]; ?></strong></label>
                        <input type="text" name="minBid" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["bidding"]["limitDate"]; ?></strong></label>
                        <input type="date" name="limitDate" class="contact_input" value="yyyy-mm-dd" />
                    </div>

                    <div class="form_row">
                        <input type="button" onclick="insertBiddingg()" svalue="<?php print $lang["general"]["accept"]; ?>" class="contact">
                    </div>
                </form>
            </div>
        <div class="bottom_prod_box_big"></div>
    </div>
</div><!-- end of center content -->
