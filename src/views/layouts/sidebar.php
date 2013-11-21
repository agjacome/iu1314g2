<div class="left_content">
    <div class="title_box"><?php print $lang ["head"]["advanced_search"]; ?></div>
    <ul class="left_menu">
<?php if ($logged) { ?>
        <li class="even"><a href="/index.php?controller=user&action=logout"><?php print $lang ["head"]["cerrar_sesion"]; ?></a></li>
<?php } else { ?>
        <li class="odd"><a href="/index.php?controller=user&action=create"><?php print $lang ["head"]["registro"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=user&action=login"><?php print $lang["head"]["login"]; ?></a></li>
<?php } ?>
        <li class="odd"><a href="subastasactivas.php"><?php print $lang ["head"]["subastas"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=product&action=available"><?php print $lang ["head"]["productos"]; ?></a></li>
<?php if (isset($userrole) && $userrole === "admin") { ?>
        <li class="odd"><a href="/index.php?controller=store&action=changeCommission"><?php print $lang["head"]["administrar_porcentajes"]; ?></a></li>
        <li class="odd"><a href="eliminarproducto.php"><?php print $lang["head"]["eliminar_productos"]; ?></a></li>
<?php } ?>
    </ul>
</div><!-- end of left content -->
