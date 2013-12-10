<p><?php echo $app->description; ?></p>

<h2>Forms</h2>
<?php echo Html::box("/forms-admin/editform/".$app->id,"New Form",true); ?><br/>
<?php echo isset($formstable) ? $formstable : "No Forms defined yet."; ?>

<h2>Members</h2>
<?php echo Html::box("/forms-admin/editapplicationmember","Add Member",true); ?><br/>
<?php echo isset($membertable) ? $membertable : "No Members added to this application yet."; ?>
