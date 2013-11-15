<?php

/**
 * User object
 * 
 * @author admin
 *
 */
class User extends DbObject {

    public $login;
    public $is_admin;
    public $password;
    public $is_active;
    public $dt_lastlogin;
    public $dt_created;
    public $contact_id;
    public $is_deleted;
    public $is_group;
    public $password_reset_token;
    public $_roles;
    public $_contact;
    public $_modifiable;

    public function delete($force = false) {
        $contact = $this->getContact();
        if ($contact) {
            $contact->delete();
        }
        $this->is_deleted = 1;
        $this->is_active = 0;
        $this->password = "";
        $this->update();
    }

    public function getContact() {
        if (!$this->_contact) {
            $this->_contact = $this->getObject("Contact", $this->contact_id);
        }
        return $this->_contact;
    }

    public function isInGroups($group_id = null) {
        $groupUsers = isset($group_id) ? $this->getObjects("GroupUser", array('user_id' => $this->id, 'group_id' => $group_id)) : $this->getObjects("GroupUser", array('user_id' => $this->id));

        if ($groupUsers) {
            return $groupUsers;
        }
        return null;
    }

    public function inGroup($group) {
        $groupmembers = $this->Auth->getGroupMembers($group->id, null);

        if ($groupmembers) {
            foreach ($groupmembers as $member) {
                if ($member->user_id == $this->id)
                    return true;

                $usr = $this->Auth->getUser($member->user_id);
                if ($usr->is_group == "1")
                    $flg = $this->inGroup($usr);
                if ($flg)
                    return true;
            }
        }
    }

    public function getFirstName() {
        $contact = $this->getContact();

        if ($contact) {
            $name = $contact->getFirstName();
        }
        return $name;
    }

    public function getSurname() {
        $contact = $this->getContact();
        if ($contact) {
            $name = $contact->getSurname();
        }
        return $name;
    }

    public function getFullName() {
        $contact = $this->getContact();
        $name = ucfirst($this->login);
        if ($contact) {
            $name = $contact->getFullName();
        }
        return $name;
    }

    public function getSelectOptionTitle() {
        return $this->getFullName();
    }

    public function getShortName() {
        $contact = $this->getContact();
        $name = ucfirst($this->login);
        if ($contact) {
            $name = $contact->firstname;
        }
        return $name;
    }

    public function getRoles($force = false) {
        if ($this->is_admin) {
            return $this->Auth->getAllRoles();
        }
        if (!$this->_roles || $force) {
            $this->_roles = array();

            $groupUsers = $this->isInGroups();

            if ($groupUsers) {
                foreach ($groupUsers as $groupUser) {
                    $groupRoles = $groupUser->getGroupRoles();

                    foreach ($groupRoles as $groupRole) {
                        if (!in_array($groupRole, $this->_roles))
                            $this->_roles[] = $groupRole;
                    }
                }
            }
            $rows = $this->getObjects("UserRole", array("user_id", $this->id), true);

            if ($rows) {
                foreach ($rows as $row) {
                    if (!in_array($row->role, $this->_roles))
                        $this->_roles[] = $row->role;
                }
            }
        }
        return $this->_roles;
    }

    public function updateLastLogin() {
        $data = array("dt_lastlogin" => $this->time2Dt(time()));
        $this->_db->update("user", $data)->where("id", $this->id)->execute();
    }

    public function hasRole($role) {
        if ($this->is_admin) {
            return true;
        }
        if ($this->getRoles()) {
            return in_array($role, $this->_roles);
        } else {
            return false;
        }
    }

    public function hasAnyRole($roles) {
        if ($this->is_admin) {
            return true;
        }
        if ($roles) {
            foreach ($roles as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function addRole($role) {
        if (!$this->hasRole($role)) {
            $ur = new UserRole($this->w);
            $ur->user_id = $this->id;
            $ur->role = $role;
            $ur->insert();
        }
    }

    public function removeRole($role) {
        if ($this->hasRole($role)) {
            $this->_db->delete("user_role")->where("user_id", $this->id)->and("role", $role)->execute();
            $this->getRoles(true);
        }
    }

    public function allowed(&$w, $path) {
        if (!$this->is_active) {
            return false;
        }
        if ($this->is_admin) {
            return true;
        }
        if ($this->getRoles()) {
            foreach ($this->getRoles() as $rn) {
                $rolefunc = "role_" . $rn . "_allowed";
                if (function_exists($rolefunc)) {
                    if ($rolefunc($w, $path)) {
                        return true;
                    }
                } else {
                    $w->logError("Role '" . $rn . "' does not exist!");
                }
            }
        }
        return false;
    }

    /**
     * encrypt the password using sha1 and a global salt.
     * 
     * @param unknown $password
     * @return string
     */
    static public function encryptPassword($password) {
        global $PASSWORD_SALT;
        return sha1($PASSWORD_SALT . $password);
    }

    public function setPassword($password) {
        $this->password = User::encryptPassword($password); //$this->encryptPassword($password);
    }

}
