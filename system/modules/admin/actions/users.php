<?php
function users_GET(Web &$w) {
	$w->Admin->navigation($w,"Users");

	$header = array("Login","First Name","Last Name",array("Admin",true),array("Active",true),array("Created", true),array("Last Login", true),"Operations");
//	$result = $w->db->sql("select user.id as id,login,firstname,lastname,is_admin,is_active,user.dt_created as dt_created,dt_lastlogin from user left join contact on user.contact_id = contact.id where user.is_deleted = 0 AND user.is_group = 0")->fetch_all();
        // $result = $w->db->get("user")->select("user.*, contact.*")->leftJoin("contact on user.contact_id = contact.id")->where("user.is_deleted", 0)->where("user.is_group", 0)->fetch_all();
        $users = $w->Admin->getObjects("User", array("is_deleted" => 0, "is_group" => 0));
        $data = array();
	foreach ($users as $user) {
            $contact = $user->getContact();
            $firstName='';
            $lastName='';
            if (!empty($contact)) {
				$firstName=$contact->firstname;
				$lastName=$contact->lastname;
			}
            $data[] = array(
                $user->login, $firstName, $lastName,
                array($user->is_admin ? "X" : "", true),
                array($user->is_active ? "X" : "", true),
                array($w->Admin->time2Dt($user->dt_created), true),
                array($w->Admin->time2Dt($user->dt_lastlogin), true),
                "<a class='button tiny editbutton' href='/admin/useredit/".$user->id."' >Edit</a>".
                "<a class='button tiny permissionsbutton' href='/admin/permissionedit/".$user->id."' >Permissions</a>".
                "<a class='button tiny deletebutton' href='/admin/userdel/".$user->id."' onclick='return confirm(\"Are you sure to delete this user?\");' >Delete</a>"
                //Html::box($w->localUrl("/admin/useredit/".$user->id."/box"),"Edit",true) . 
                //Html::b("/admin/permissionedit/".$user->id,"Permissions") . 
                //Html::b($w->localUrl("/admin/userdel/".$user->id),"Delete","Are you sure to delete this user?")
            );
	}
	$w->ctx("table", Html::table($data, null, "tablesorter", $header));
}

     
 
