<div class="contact_form">
<div class="center_title_bar"><?php print $lang["product"]["update"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form action="index.php" method="post" accept-charset="utf-8">
                    <input type="hidden" name="controller" value="product" />
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="id" value="<?php print $product->getId(); ?>" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["product"]["name"]; ?></strong></label>
                        <input type="text" name="name" class="contact_input" value="<?php print $product->name; ?>" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["product"]["descr"]; ?></strong></label>
                        <textarea rows="8" cols="50" name="description"><?php print $product->description; ?></textarea>
                    </div>

                    <div class="form_row">
                        <input type="submit" value="<?php print $lang["general"]["accept"]; ?>" class="contact">
                    </div>
                </form>
            </div>
        <div class="bottom_prod_box_big"></div>
    </div>
</div><!-- end of center content -->
