<?php
if ($w->Auth->user()->allowed($w,"/inbox/send")) {
	print Html::b($webroot."/inbox/send","Create Message");
}
print "<button onclick='sendArch()'>Archive</button>";//Html::b($webroot."/inbox/archive","Archive",null,'archive');
print "<button onclick='deleteMessage()'>Delete</button>";
if ($read) {
	$readqlines = array(array("<input type='checkbox' id='allChk' onclick='selectAll()' />","Subject","Date","Sender"));
	$total_read_count = 0;
	foreach ($read as $q => $in) {
		$line = array();
		$line[]="<input type='checkbox' id='".$in->id."' value='".$in->id."' class='classChk'/>";
		$line[]=Html::a(WEBROOT."/inbox/view/read/".$in->id,$in->subject);
		$line[]=$in->getDate("dt_created","d/m/Y H:i");
		$line[]=($in->sender_id ? $in->getSender()->getFullName() : "");
		$readqlines[]=$line;
	}
	print Html::table($readqlines,null,"tablesorter",false);
}
	$last_page = ceil($readtotal/40);
	$minPage = ($pgnum*1)-5;
	($minPage <= 0) ? $minPage = 1 : '';
	//print $minPage . "\n";
	$maxPage = ($pgnum*1)+5;
	($maxPage > $last_page) ? $maxPage = $last_page : '';
	//print $maxPage . "\n";
	//exit();
	
	if ($last_page > 1){
		print "<table style='margin:auto;'><tr id='nav'>";
		if($pgnum > 1){
			print "<td style='background-color:#eee;' id='link".$i." prevlink' class='link' onclick='switchPage(".($pgnum-1).")'><a class='link'  href='#'>Prev</a></td>&nbsp";
		}
		for($i=$minPage;$i<=$maxPage;$i++){
			if ($pgnum == $i){
				print "<td id='link".$i." ' class='link ispage' ><b>*".$i."*</b></td>&nbsp";
			} else {
				print "<td id='link".$i."' class='link' onclick='switchPage(".$i.")'><a class='link'  href='#'>".$i."</a></td>&nbsp";
			}
		}
		if ($pgnum < $last_page && $last_page !== 1){
			print "<td style='background-color: #eee; width:30px;' id='link".$i." nextlink' class='link' onclick='switchPage(".($pgnum+1).")'><a class='link'  href='#'>Next</a></td>&nbsp";
		}
		print "</tr></table>";
	}
print "</tr></table>";
?>

</div>
</div>
<script type='text/javascript'>
	$(".ispage").css("cursor","default");
	$(".ispage").hover(function(){$(this).css("background-color","#CAFF70")});
	$(document).ready(function(){
		for(var i=1; i<<?php echo !empty($pgcount) ? $pgcount : 1; ?>+1; i++){
			if (i == 1){
				$("#link"+i).addClass('selectedPage');
			} else {
				$("#link"+i).removeClass('selectedPage');
			}
		}
	});

	function switchPage(page){
		window.location.href = "<?php echo $webroot; ?>/inbox/read/"+page ;
	}
	
	function selectAll(){
		$(":checkbox").attr("checked","checked");
	}

	function sendArch(){
		var check = new Array();
		var count = 0;
		$(":checkbox:checked").each(function(){
			check[count] = $(this).val();
			count++;
		});
		if (count !== 0){
			window.location.href = "<?php echo $webroot; ?>/inbox/archive/read/"+check;
		}
	}

	function deleteMessage(){
		var check = new Array();
		var count = 0;
		$(":checkbox:checked").each(function(){
			check[count] = $(this).val();
			count++;
		});
		if (count !== 0){
			window.location.href = "<?php echo $webroot; ?>/inbox/delete/read/"+check;
		}
	}
</script>
