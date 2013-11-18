<?php
require "header.php";
require "sidebar.php";
?>

<ul>
<?php foreach ($list as $user) { ?>
    <li>
        <?php print $lang["user"]["username"]  . ":" . $user["login"]; ?><br>
        <?php print $lang["user"]["email"]     . ":" . $user["email"]; ?><br>
        <a href="index.php?controller=user&action=get&login=<?php print $user["login"]; ?>"><?php print $lang["user"]["details"]; ?></a>
    </li>
<?php } ?>
</ul>

<?php
require "footer.php";
?>
