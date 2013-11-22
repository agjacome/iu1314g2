<div class="contact_form">
<div class="center_title_bar"><?php print $lang["sale"]["buy"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form method="post" action="index.php" accept-charset="utf-8">
                    <input type="hidden" name="controller" value="sale" />
                    <input type="hidden" name="action" value="purchase" />
                    <input type="hidden" name="sale" value="<?php print $sale->getId(); ?>" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["sale"]["stock"]; ?></strong></label>
                        <select name="quantity">
                            <?php for ($i = 1; $i <= $sale->stock; $i++) { ?>
                                <option value="<?php print $i; ?>"><?php print $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
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
