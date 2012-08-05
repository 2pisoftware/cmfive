<div class="tabs">
    <div class="tab-head">
        <a href="#" class="active" >View</a>
        <a href="<?=WEBROOT."/wiki/wikichanges/".$wiki->id."/".$page->name?>" >Wiki History</a>
        <a href="<?=WEBROOT."/wiki/pagechanges/".$wiki->id."/".$page->name?>" >Page History</a>
        <?if ($wiki->canEdit($w->Auth->user())):?>
        <a href="<?=WEBROOT."/wiki/edit/".$wiki->name."/".$page->name?>" >Edit</a>
        <?endif;?>
        <? if ($wiki->isOwner($w->Auth->user()) && $page->name == "HomePage"):?>
        <a href="<?=WEBROOT."/wiki/members/".$wiki->id?>" >Members</a>
        <? endif;?>        
        
    </div>
    <div class="tab-body">
        <div id="tab-1">
        	<div style="font-size:8pt;color:gray;">
        	<a href="<?=WEBROOT."/wiki/view/".$wiki->name."/HomePage"?>" >Home</a>
        	<?
        		if ($_SESSION['wikicrumbs'][$wiki->name]) {
        			foreach(array_keys($_SESSION['wikicrumbs'][$wiki->name]) as $pn) {
        				echo " &gt; <a href=\"".WEBROOT."/wiki/view/".$wiki->name."/".$pn."\">".$pn."</a>";
        			}
        		}
        	?>
        	</div>
        	<div>
			<?=$body?>
			</div>
			<?if ($attachments):?>
			<table class="tablesorter">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach($attachments as $att):?>
                <tr>
                    <td>
                    	<?if ($att->isImage()):?>
                    	<a href="<?=$webroot?>/file/atthumb/<?=$att->id?>/1024/768/a.jpg" rel="gallery">
                    	<?else:?>
                    	<a href="<?=$webroot?>/file/atfile/<?=$att->id?>/<?=$att->filename?>">
                    	<?endif;?>
                    	<?=$att->filename?></a>
                    </td>
                    <td>
                       	<?=$att->description?></a>
                    </td>
                </tr>
                <?endforeach;?>
                </tbody>
            </table>
            <?endif;?>
			<p></p>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("a[rel='gallery']").colorbox();
</script>     
