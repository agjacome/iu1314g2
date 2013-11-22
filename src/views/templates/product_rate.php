<div class="center_content">
    <div class="center_title_bar"><?php print $lang ["product"]["rate"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <form id="puntuacion" method="post" action="/index.php">
                <div class="contact_form">
                    <input type="hidden" name="controller" value="product" />
                    <input type="hidden" name="action"     value="rate"    />
                    <input type="hidden" name="prod"       value="<?php print $product->getId(); ?>" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["product"]["rating"]; ?></strong></label>
                        <select name="rating" size=1>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["product"]["comment"]; ?></strong></label>
                        <textarea rows="8" cols="50" name="comment"></textarea>
                    </div>
                    <div class="form_row">
                        <input type=submit value="<?php print $lang["general"]["accept"]; ?>">
                    </div>
                </div>
            </form>
        </div>
        <div class="bottom_prod_box_big"></div>
    </div>
</div><!-- end of center content -->
