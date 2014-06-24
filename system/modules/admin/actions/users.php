<?php
function users_GET(Web &$w) {
	$w->Admin->navigation($w,"Users");

	$header = array("Login","First Name","Last Name",array("Admin",true),array("Active",true),array("Created", true),array("Last Login", true),"Operations");
//	$result = $w->db->sql("select user.id as id,login,firstname,lastname,is_admin,is_active,user.dt_created as dt_created,dt_lastlogin from user left join contact on user.contact_id = contact.id where user.is_deleted = 0 AND user.is_group = 0")->fetch_all();
    $result = $w->db->get("user")->select("user.*, contact.*")->leftJoin("contact on user.contact_id = contact.id")->where("user.is_deleted", 0)->where("user.is_group", 0)->fetch_all();
        $users = array();
	foreach ($result as $user) {
		$line = array();
		$line[]=$user['login'];
		$line[]=$user['firstname'];
		$line[]=$user['lastname'];
		$line[]= array($user['is_admin'] ? "X" : "", true);
		$line[]=array($user['is_active'] ? "X" : "", true);
		$line[]=array($user['dt_created'], true);
		$line[]=array($user['dt_lastlogin'], true);
		$view = Html::box($w->localUrl("/admin/useredit/".$user['id']."/box"),"Edit",true);
		$view .= Html::b("/admin/permissionedit/".$user['id'],"Permissions");
		$view .= Html::b($w->localUrl("/admin/userdel/".$user['id']),"Delete","Are you sure to delete this user?");
		$line[]=$view;
		$users[]=$line;
	}
	$w->ctx("table",Html::table($users,null,"tablesorter",$header));
}