<style>
<!--
.forms-app-box {
	width: 128px;
	height: 155px;
	
	float: left;
	margin: 12px;
}
-->
</style>

<?if ($apps) { foreach($apps as $app):?>
<div class="forms-app-box">
<a href="<?=$w->localUrl("/forms/app/".$app->slug)?>"><img src="<?=FormsLib::getApplicationIcon($app)?>" width="128" height="128" border="0"/></a>
<center><?=Html::a($w->localUrl("/forms/app/".$app->slug),$app->title);?></center>
</div>
<?endforeach;}?>