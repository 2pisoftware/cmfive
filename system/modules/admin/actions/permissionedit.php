<?php

function permissionedit_GET(Web $w) {
    $option = $w->pathMatch("group_id");

    $user = $w->Auth->getUser($option['group_id']);

    $userName = $user->is_group == 1 ? $user->login : $user->getContact()->getFullName();

    AdminLib::navigation($w, "Permissions - " . $userName);

    //fill in permission tables;
    $groupUsers = $w->Auth->getUser($option['group_id'])->isInGroups();
    $groupRoles = array();
    if ($groupUsers) {
        foreach ($groupUsers as $groupUser) {
            $grs = $groupUser->getGroup()->getRoles();

            foreach ($grs as $gr) {
                $groupRoles[] = $gr;
            }
        }
    }

    $roles = $w->Auth->getAllRoles();

    foreach ($roles as $role) {
        $characters = explode("_", $role);

        if (count($characters) == 1)
            array_unshift($characters, "admin");

        $result[$characters[0]][] = $characters[1];
    }

    foreach ($result as $module => $characters) {
        $characters = array_chunk($characters, 4);

        foreach ($characters as $level => $character) {
            foreach ($character as $r) {
                $roleName = $module == "admin" ? $r : implode("_", array($module, $r));

                $permission[ucwords($module)][$level][] = array($roleName, "checkbox", "check_" . $roleName, $w->Auth->getUser($option['group_id'])->hasRole($roleName));
            }
        }
    }
    $action = $w->Auth->user()->is_admin ? "/admin/permissionedit/" . $option['group_id'] : null;

    $w->ctx("permission", Html::multiColForm($permission, $action, "POST", "Save", null, null, array('goBack' => 'Go Back')));

    $w->ctx("groupRoles", json_encode($groupRoles));
}

function permissionedit_POST(Web &$w) {
    $option = $w->pathMatch("group_id");
    //update permissions for user/group;
    $user = $w->Auth->getUser($option['group_id']);
    //add roles;
    $roles = $w->Auth->getAllRoles();
    foreach ($roles as $r) {
        if (!empty($_POST["check_" . $r])) {
            if ($_POST["check_" . $r] == 1) {
                $user->addRole($r);
            }
        }
    }
    //remove roles;
    $userRoles = $user->getRoles();

    foreach ($userRoles as $userRole) {
        if (!$_POST["check_" . $userRole]) {
            $user->removeRole($userRole);
        }
    }
    $returnPath = $user->is_group == 1 ? "/admin/moreInfo/" . $option['group_id'] : "/admin/users";

    $w->msg("Permissions are updated!", $returnPath);
}
