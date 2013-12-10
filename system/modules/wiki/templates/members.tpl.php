<div class="tabs">
    <div class="tab-head">
        <a href="<?php echo WEBROOT."/wiki/index"; ?>">List</a>
        <a href="<?php echo WEBROOT."/wiki/view/$wiki->name/HomePage"; ?>">Home</a>
        <a class="active" href="#" >Members</a>
    </div>
    <div class="tab-body">
    
		<?php // ============== Text Edit ===================================== ?>            
        <div id="tab-1">
			<?php echo Html::box(WEBROOT."/wiki/editmember/".$wiki->id,"Add Member",true); ?>
			<?php if ($wiki->getUsers()) : ?>
			<table class="tablesorter">
				<thead>
					<tr>
						<th>Name</th>
						<th>Role</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($wiki->getUsers() as $mem) : ?>
					<tr>
						<td><?php echo $mem->getFullName(); ?></td>
						<td><?php echo $mem->role; ?></td>
						<td><?php echo Html::box($webroot."/wiki/editmember/".$wiki->id."/".$mem->id,"Edit",true); ?>&nbsp;
						<?php echo Html::b($webroot."/wiki/delmember/".$wiki->id."/".$mem->id,"Delete"); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
			<p></p>
		</div>
	</div>
</div>

