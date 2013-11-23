<div class="contact_form">
<div class="center_title_bar"><?php print $lang["sale"]["buy"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form method="post" action="index.php" accept-charset="utf-8">
                    <input type="hidden" name="controller" value="bidding" />
                    <input type="hidden" name="action" value="payBid" />
                    <input type="hidden" name="bid" value="<?php print $bid->getId(); ?>" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["sale"]["payMethod"]; ?></strong></label>
                        <select name="payMethod">
                            <option value="paypal">PayPal</option>
                            <option value="tarjeta"><?php print $lang["sale"]["creditCard"]; ?></option>
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="contact"><strong>PayPal</strong></label>
                        <input type="text" name="paypal" class="contact_input" />
                    </div>
                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["sale"]["creditCard"]; ?></strong></label>
                        <input type="text" name="creditCard" class="contact_input" />
                    </div>
                    <input type=submit value="Aceptar">
                </form>
            </div>
        </div>
    <div class="bottom_prod_box_big"></div>
</div><!-- end of center content -->
