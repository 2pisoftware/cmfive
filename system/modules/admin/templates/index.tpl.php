<b>Users Currently Logged In</b>
<ul>
    <?php if (!empty($currentUsers)):foreach($currentUsers as $u):?>
        <li>
            <?php echo $u->getFullName();?>
        </li>
    <?php endforeach;endif;?>
</ul>

<div class="panel">
    <b>Printers</b>
    <?php echo Html::box("/admin/editprinter", "Add a printer", true); ?>
    <ul>
        <?php if (!empty($printers)):foreach($printers as $p):?>
            <li>
                <?php echo $p->name . (!empty($p->server) ? " on server '" . $p->server . "'": "") . (!empty($p->port) ? " on port " . $p->port : "") ?>
                <?php echo Html::box("/admin/editprinter/{$p->id}", "Edit", true); ?>
                <?php echo Html::b("/admin/deleteprinter/{$p->id}", "Delete", "Are you sure you want to delete this printer?"); ?>
            </li>
        <?php endforeach;endif;?>
    </ul>
</div>