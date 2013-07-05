<p><?=$app->description?></p>

<h2>Forms</h2>
<?=Html::box("/forms-admin/editform/".$app->id,"New Form",true)?><br/>
<?= isset($formstable) ? $formstable : "No Forms defined yet."?>

<h2>Members</h2>
<?=Html::box("/forms-admin/editapplicationmember","Add Member",true)?><br/>
<?= isset($membertable) ? $membertable : "No Members added to this application yet."?>
