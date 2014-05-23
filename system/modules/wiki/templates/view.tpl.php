
        <?php echo Html::b(WEBROOT."/wiki/wikichanges/".$wiki->id."/".$page->name,"Wiki History");?>
        <?php echo Html::b(WEBROOT."/wiki/pagechanges/".$wiki->id."/".$page->name,"Page History");?>
        <?php if ($wiki->canEdit($w->Auth->user())):?>
        <?php echo Html::b(WEBROOT."/wiki/edit/".$wiki->name."/".$page->name,"Edit");?>
        <?php endif;?>
        <?php if ($wiki->isOwner($w->Auth->user()) && $page->name == "HomePage"):?>
        <?php echo Html::b(WEBROOT."/wiki/members/".$wiki->id,"Members");?>
        <?php endif;?>        
        
        	<div style="font-size:8pt;color:gray;">
        	<a href="<?php echo WEBROOT."/wiki/view/".$wiki->name."/HomePage"?>" >Home</a>
        	<?php 
        		if ($_SESSION['wikicrumbs'][$wiki->name]) {
        			foreach(array_keys($_SESSION['wikicrumbs'][$wiki->name]) as $pn) {
        				echo " &gt; <a href=\"".WEBROOT."/wiki/view/".$wiki->name."/".$pn."\">".$pn."</a>";
        			}
        		}
        	?>
        	</div>
        	<div>
			<?php echo $body?>
			</div>
			<?php if (!empty($attachments)):?>
			<table class="tablesorter">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($attachments as $att):?>
                <tr>
                    <td>
                    	<?php if ($att->isImage()):?>
                    	<a href="<?php echo $webroot?>/file/atthumb/<?php echo $att->id?>/1024/768/a.jpg" rel="gallery">
                    	<?php else:?>
                    	<a href="<?php echo $webroot?>/file/atfile/<?php echo $att->id?>/<?php echo $att->filename?>">
                    	<?php endif;?>
                    	<?php echo $att->filename?></a>
                    </td>
                    <td>
                       	<?php echo $att->description?></a>
                    </td>
                </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <?php endif;?>
			<p></p>
        </div>

<script type="text/javascript">
    $("a[rel='gallery']").colorbox();
</script>     
