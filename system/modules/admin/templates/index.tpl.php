<b>Users Currently Logged In</b>
<ul>
<?if ($currentUsers):foreach($currentUsers as $u):?>
<li>
<?=$u->getFullName()?>
</li>
<?endforeach;endif;?>
</ul>
