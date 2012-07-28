<p><?=$form->description?></p>

<h2>Fields</h2>
<?=Html::box("/forms-admin/editfield/".$app->id.'/'.$form->id,"New Field",true)?><br/>
<?= isset($formstable) ? $formstable : "No Fields defined yet."?>

