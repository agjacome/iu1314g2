<div class="center_content">
    <div class="center_title_bar"><?php print $lang["product"]["panel"]; ?></div>
    <ul class="list">
        <li class="even"><a href="/index.php?controller=product&action=create"><?php print $lang["product"]["new"]; ?></a></li>
        <li class="odd"><a href="/index.php?controller=product&action=owned"><?php print $lang["product"]["owned"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=sale&action=owned"><?php print $lang["sale"]["owned"]; ?></a>
        <li class="odd"><a href="/index.php?controller=bidding&action=owned"><?php print $lang["bidding"]["owned"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=sale&action=purchased"><?php print $lang["sale"]["purchased"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=bidding&action=pendingPayments"><?php print $lang["bidding"]["pending_pay"]; ?></a></li>
    </ul>
</div>
