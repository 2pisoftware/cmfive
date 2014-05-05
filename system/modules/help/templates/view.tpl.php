<ul class="breadcrumbs">
    <li><a href="<?php echo WEBROOT . '/help/toc'; ?>">Contents</a></li>
    <?php if (!empty($module_toc)): ?>
        <li><a href="<?php echo WEBROOT . '/help/view/' . $module_toc ?>"><?php echo $module_title; ?></a></li>
    <?php endif; ?>
    <li><a href="<?php echo WEBROOT . '/help/view/help/onhelp'; ?>">Help on Help</a></li>
</ul>

<?php echo $help_content; ?>
