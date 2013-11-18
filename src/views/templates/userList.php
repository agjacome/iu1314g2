<?php
require "header.php";
require "sidebar.php";
?>

<ul>
<?php foreach ($list as $user) { ?>
    <li>
        <?php print $lang["user"]["username"]  . ":" . $user["login"]; ?><br>
        <?php print $lang["user"]["role"]      . ":" . $user["role"]; ?><br>
        <?php print $lang["user"]["email"]     . ":" . $user["email"]; ?><br>
        <?php print $lang["user"]["name"]      . ":" . $user["name"]; ?><br>
        <?php print $lang["user"]["address"]   . ":" . $user["address"]; ?><br>
        <?php print $lang["user"]["telephone"] . ":" . $user["telephone"]; ?><br>
    </li>
<?php } ?>
</ul>

<?php
require "footer.php";
?>
