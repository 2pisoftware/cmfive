<script type="text/javascript">
    var current_tab = 1;
    function switchTab(num){
        if (num == current_tab) return;
        $('#tab-'+current_tab).hide()
        $('#tab-link-'+current_tab).removeClass("active");
        $('#tab-'+num).show().addClass("active");
        $('#tab-link-'+num).addClass("active");
        current_tab = num;
    }
</script>
<div class="tabs">
    <div class="tab-head">
        <a href="<?=WEBROOT."/wiki/view/".$wikiname."/".$pagename?>">View</a>
        <a id="tab-link-1" href="#" class="active" onclick="switchTab(1);">Edit</a>
    </div>
    <div class="tab-body">
    
		<?// ============== Text Edit ===================================== ?>            
        <div id="tab-1">
			<form action="<?=WEBROOT."/wiki/edit/".$wikiname."/".$pagename?>" method="POST">
			<textarea style="width:80%;height:500px;" name="body"><?=$page->body?></textarea>
			<p></p>
			<input type="submit" value="Save">
			<?=Html::b(WEBROOT."/wiki/view/".$wikiname."/".$pagename,"Cancel")?> 
			<?=Html::box(WEBROOT."/wiki/markup","Markup Help",true)?>
			<?=Html::box($webroot."/file/attach/WikiPage/".$page->id."/wiki+edit+".$wiki->name."+".$page->name,"Attach a File",true)?>		
			</form>
			<p></p>			
			<?if ($attachments):?>
			<table class="tablesorter">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Description</th>
                        <th>Action</th>
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
                    <td><?=Html::a($webroot."/file/atdel/".$att->id."/wiki+edit+".$wiki->name."+".$page->name,"Delete",null,null,"Do you want to delete this attachment?")?></td>
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

