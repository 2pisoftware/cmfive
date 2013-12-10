<div class="tabs">
    <div class="tab-head">
        <a href="<?php echo WEBROOT."/wiki/view/".$wiki->name."/".$pagename; ?>" >Back</a>
        <a href="#" class="active" >Wiki History</a>        
    </div>
    <div class="tab-body">
        <div id="tab-1">
        	<?php 
        	$hist = $wiki->getHistory();
        	if ($hist){
	        	$table[]=array(
				"Date",
				"Page",
				"User");
				foreach($hist as $wh) {
					$table[]=array(
    					$wh['day']."/".$wh['month']."/".$wh['year'],
    					Html::a(WEBROOT."/wiki/view/".$wiki->name."/".$wh['name'],"<b>".$wh['name']."</b>"),
    					$w->Auth->getUser($wh['creator_id'])->getFullName()
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