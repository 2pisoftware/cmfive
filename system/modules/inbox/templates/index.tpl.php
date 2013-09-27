<style type="text/css">
a.link:link {
	color: #000;
}

a.link:visited {
	color: #000;
}

.link {
	float: centre;
	width: 30px;
}

.ispage {
	background-color: #CAFF70;
	cursor: default;
}
/*
a.link:hover {
	color: #66AA00;
	font-weight: bold;
}
*/
a.link:active {
	color: #000;
}
thead {
	font-weight: bold;
}
.selectedPage {
	background-color: #99CC00;
}
/*
#pageNoTable {
	width: 100%;
	border: thin green solid;
	padding-bottom: 0px;
	padding-left: 1px;
}
#pageNoTable td {
	text-align: center;
	height: 25px;
}
#pageNoTable td:hover {
	background-color: #99CC00;
	cursor: pointer;
}
*/

#nav td{
	color:#fff;
	/*font-size:1.6em;
	font-weight:bold;*/
	padding:10px;
	/*display:block;*/
	text-decoration:none;
	background:#34ad4a;
	text-align:center;
	/*text-shadow:1px 1px 1px #1c5e28;*/
	background:-webkit-gradient(linear, left top, left bottom, from(#47b649), to(#34ad4a));
	-webkit-transition: -webkit-transform 0.1s ease-in; /* Tells webkit to make a transition of a transform */
}

#nav td:first-of-type {
	-moz-border-radius-topleft:5px;
	-moz-border-radius-bottomleft:5px;
	-webkit-border-top-left-radius:5px;
	-webkit-border-bottom-left-radius:5px;
	border-top-left-radius:5px;
	border-bottom-left-radius:5px;
}

#nav td:last-of-type {
	-moz-border-radius-topright:5px;
	-moz-border-radius-bottomright:5px;
	-webkit-border-top-right-radius:5px;
	-webkit-border-bottom-right-radius:5px;
	border-top-right-radius:5px;
	border-bottom-right-radius:5px;
}

#nav td:hover{
	background:#0079ac;
	/*text-shadow:1px 1px 1px #0d4f6b;*/
	background:-webkit-gradient(linear, left top, left bottom, from(#0ba7be), to(#0079ac));
	-moz-transform:scale(1.05);
	-webkit-transform:scale(1.05);
	/* -moz-box-shadow:0 0 5px #666;
	-webkit-box-shadow:0 0 5px #666;*/
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	cursor: pointer;
}

</style>

<?php
if ($w->Auth->user()->allowed($w,"/inbox/send")) {
	print Html::b($webroot."/inbox/send","Create Message");
}
print "<button onclick='sendArch()'>Archive</button>";
print "<button onclick='deleteMessage()'>Delete</button>";
if($w->service('Inbox')->inboxCountMarker()){
	print "&nbsp;";
	print Html::b($w->localUrl("/inbox/allread"),"Mark all read","Are you sure to mark all messages as read?");
}
if (!empty($new)) {
	$newqlines = array(array("<input type='checkbox' id='allChk' onclick='selectAll()' />","Subject","Date","Sender"));
	$total_new_count = 0;
	foreach ($new as $q => $in) {
		$line = array();
		$line[]="<input type='checkbox' id='".$in->id."' value='".$in->id."' class='classChk'/>";
		$line[]=Html::a(WEBROOT."/inbox/view/new/".$in->id,"<b>".$in->subject."</b>");;
		$line[]="<b>".$in->getDate("dt_created","d/m/Y H:i")."</b>";
		$line[]="<b>".($in->sender_id ? $in->getSender()->getFullName() : "")."</b>";
		$newqlines[]=$line;
	}
	print Html::table($newqlines,null,"tablesorter",false);
	$last_page = ceil($newtotal/40);
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
} else {
	$url = $webroot."/inbox/read";
	print "<br/><br/>No new messages, <a href='".$url."'>click here</a> to go to your read messages.";
}

?>
</div>
</div>

<script type='text/javascript'>
	$(".ispage").css("cursor","default");
	$(".ispage").hover(function(){$(this).css("background-color","#CAFF70")});
	$(document).ready(function(){
		for(var i=1; i<<?=$pgcount;?>+1; i++){
			if (i == 1){
				$("#link"+i).addClass('selectedPage');
			} else {
				$("#link"+i).removeClass('selectedPage');
			}
		}
	});

	function switchPage(page){
		window.location.href = "<?=$webroot;?>/inbox/index/"+page;
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
			window.location.href = "<?=$webroot;?>/inbox/archive/index/"+check;
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
			window.location.href = "<?=$webroot;?>/inbox/delete/index/"+check;
		}
	}
</script>