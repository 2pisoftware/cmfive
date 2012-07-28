<div class="tabs">
    <div class="tab-head">
        <a href="<?=WEBROOT."/wiki/index"?>" >List</a>
        <a href="<?=WEBROOT."/wiki/view/$wiki->name/HomePage"?>" >Home</a>
        <a  class="active" href="#" >Members</a>
    </div>
    <div class="tab-body">
    
		<?// ============== Text Edit ===================================== ?>            
        <div id="tab-1">
			<?=Html::box(WEBROOT."/wiki/editmember/".$wiki->id,"Add Member",true)?>
			<?if ($wiki->getUsers()):?>
			<table class="tablesorter">
				<thead>
					<tr>
						<th>Name</th>
						<th>Role</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?foreach($wiki->getUsers() as $mem):?>
					<tr>
						<td><?=$mem->getFullName()?></td>
						<td><?=$mem->role?></td>
						<td><?=Html::box($webroot."/wiki/editmember/".$wiki->id."/".$mem->id,"Edit",true)?>&nbsp;
						<?=Html::b($webroot."/wiki/delmember/".$wiki->id."/".$mem->id,"Delete")?></td>
					</tr>
					<?endforeach;?>
				</tbody>
			</table>
			<?endif;?>
			<p></p>
		</div>
	</div>
</div>

