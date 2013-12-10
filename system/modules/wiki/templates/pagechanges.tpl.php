<div class="tabs">
    <div class="tab-head">
        <a href="<?php echo WEBROOT."/wiki/view/".$wiki->name."/".$pagename; ?>" >Back</a>
        <a href="#" class="active" >Page History</a>        
    </div>
    <div class="tab-body">
        <div id="tab-1">
        	<?php 
        	$hist = $page->getHistory();
        	if ($hist){
	        	$table[]=array(
				"Date",
				"User",
	        	"Action");
				foreach($hist as $ph) {
					$table[]=array(
    					$ph->getDateTime("dt_created","d/m/Y H:i"),
    					$w->Auth->getUser($ph->creator_id)->getFullName(),
    					Html::box(WEBROOT."/wiki/pageversion/".$wiki->name."/".$ph->id,"View",true),
					);
				}
				echo Html::table($table,"history","tablesorter",true);
        	} else {
        		echo "No changes yet.";
        	}
			?>
        </div>
    </div>
</div>