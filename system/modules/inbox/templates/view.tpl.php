<div class="tabs">
	<div class="tab-head">
		<a id="tab-link-1" href="#" class="active" onclick="switchTab(1);">Message</a>
	</div>
	<div class="tab-body">

	<?
	print $w->menuButton("inbox/"/*.$row['status']*/,"Back");

	if ($w->Auth->user()->allowed($w,"/inbox/send")) {
		print $w->menuButton("inbox/send/"."$message->id","Reply");
	}
	print $w->menuButton("inbox/archive/".$type."/".$message->id,"Archive");
	
	print $w->menuButton("inbox/delete/".$type."/".$message->id,"Delete");
	
	$qlines = array(array("Subject","Date","Sender"));
	$line = array();
	$line[]="<b>".$message->subject."</b>";
	$line[]=$message->getDate("dt_created","d/m/Y H:i");
	$line[]=$message->sender_id ? Html::a(WEBROOT."/contact/view/".$message->getSender()->contact_id,$message->getSender()->getFullName()) : "";
	$qlines[]=$line;

	print Html::table($qlines,null,"tablesorter",true);
	?>

		<div class="tab-body" style="margin-bottom: 20px; padding: 10px;">
		<?=$message->getMessage()?>
		</div>
		<?
		$parent_id = $message->parent_message_id;
		$parent_id = $message->parent_message_id;
	
	if ($parent_id){
		print "<div class='tab-body' style='width: 500px; margin-bottom: 20px; padding: 10px;'>";
		print "<b><u> Previous Messages </u></b><br/><hr/>";
		$counter = 1;
		while (!$parent_id == 0 || !$parent_id == null){
			if ($counter % 2 != 0){
				$bgcolor = "#ddd";
			} else {
				$bgcolor = "white";
			}
			$parent_message = $w->Inbox->getMessage($parent_id);
			print "<div style='padding:3px; background-color: ".$bgcolor."';'> Message sent by: <i>" . $w->Auth->getUser($parent_message->sender_id)->getFullname() . "</i>  on: <i>" . $parent_message->getDate("dt_created","d/m/Y H:i") . "</i><br/>";
			print $parent_message->getMessage();
			print "</div>";
			$parent_id = $parent_message->parent_message_id ? $parent_message->parent_message_id : null;
			$counter++;
		}
		print "</div>";
	}
		
		?>
	</div>
</div>
