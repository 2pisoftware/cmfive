<?php

/**
 * Display User edit form
 *
 * @param <type> $w
 */
function useredit_GET(Web &$w) {
    $p = $w->pathMatch("id", "box");
    $user = $w->Auth->getObject("User", $p["id"]);
    if ($user) {
        AdminLib::navigation($w, "Administration - Edit User - " . $user->login);
    } else {
        if (!$p['box']) {
            $w->error("User " . $w->ctx("id") . " does not exist.", "/admin/users");
        }
    }
    $w->ctx("user", $user);

    // no layout if displayed in a box
    if ($p['box']) {
        $w->setLayout(null);
    }
}

/**
 * Handle User Edit form submission
 *
 * @param <type> $w
 */
function useredit_POST(Web &$w) {
    $w->pathMatch("id");
    $errors = $w->validate(array(
        array("login", ".+", "Login is mandatory")
    ));
    if ($_REQUEST['password'] && ($_REQUEST['password'] != $_REQUEST['password2'])) {
        $error[] = "Passwords don't match";
    }
    $user = $w->Auth->getObject("User", $w->ctx('id'));
    if (!$user) {
        $errors[] = "User does not exist";
    }
    if (sizeof($errors) != 0) {
        $w->error(implode("<br/>\n", $errors), "/admin/useredit/" . $w->ctx("id"));
    }
    $user->login = $_REQUEST['login'];

    $user->fill($_REQUEST);
    if ($_REQUEST['password']) {
        $user->setPassword($_REQUEST['password']);
    } else {
        $user->password = null;
    }
    $user->is_admin = isset($_REQUEST['is_admin']) ? 1 : 0;
    $user->is_active = isset($_REQUEST['is_active']) ? 1 : 0;
    $user->update();

    // adding roles
    $roles = $w->Auth->getAllRoles();
    foreach ($roles as $r) {
        if (!empty($_POST["check_" . $r])) {
            if ($_REQUEST["check_" . $r] == 1) {
                $user->addRole($r);
            }
        }
    }
    // deleting roles
    foreach ($user->getRoles() as $r) {
        if (!$_REQUEST["check_" . $r]) {
            $user->removeRole($r);
        }
    }

    $contact = $user->getContact();
    if ($contact) {
        $contact->fill($_REQUEST);
        $contact->private_to_user_id = null;
        $contact->update();
    }

    $w->msg("User " . $user->login . " updated.", "/admin/users");
}
