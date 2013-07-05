<?php
function users_GET(Web &$w) {
	AdminLib::navigation($w,"Users");

	$users = array(array("Login","First Name","Last Name","Admin","Active","Created","Last Login","Operations"));
	$result = $w->db->sql("select user.id as id,login,firstname,lastname,is_admin,is_active,user.dt_created as dt_created,dt_lastlogin from user left join contact on user.contact_id = contact.id where user.is_deleted = 0 AND user.is_group = 0")->fetch_all();
	foreach ($result as $user) {
		$line = array();
		$line[]=$user['login'];
		$line[]=$user['firstname'];
		$line[]=$user['lastname'];
		$line[]=$user['is_admin'] ? "X" : "";
		$line[]=$user['is_active'] ? "X" : "";
		$line[]=$user['dt_created'];
		$line[]=$user['dt_lastlogin'];
		$view = Html::box($w->localUrl("/admin/useredit/".$user['id']."/box"),"Edit",true)."&nbsp;";
		$view .= Html::b("/admin/permissionedit/".$user['id'],"Permissions")."&nbsp;";
		$view .= "&nbsp;".Html::b($w->localUrl("/admin/userdel/".$user['id']),"Delete","Are you sure to delete this user?")."&nbsp;";
		$line[]=$view;
		$users[]=$line;
	}
	$w->ctx("table",Html::table($users,null,"tablesorter",true));
}