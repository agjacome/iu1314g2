<div class="contact_form">
<div class="center_title_bar"><?php print $lang["sale"]["create"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form action="index.php" name="insertSale"method="post" accept-charset="utf-8">
                    

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["sale"]["price"]; ?></strong></label>
                        <input type="text" name="price" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["sale"]["stock"]; ?></strong></label>
                        <input type="text" name="stock" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <input type="button" onClick="insertSalee();" value="<?php print $lang["general"]["accept"]; ?>" class="contact">
                    </div>
                    <input type="hidden" name="controller" value="sale" />
                    <input type="hidden" name="action"     value="create" />
                    <input type="hidden" name="product"    value="<?php print $product->getId(); ?>" />
                </form>
            </div>
        <div class="bottom_prod_box_big"></div>
    </div>
</div><!-- end of center content -->
