<?php
class AuthService extends DbService {

	var $_roles;
	var $_roles_loaded = false;
	var $_user = null;

	function login($login, $password, $client_timezone) {
		$password = User::encryptPassword($password);
		$user_data = $this->_db->get("user")->where("login",$login)->and("password",$password)->and("is_active","1")->and("is_deleted","0")->fetch_row();
		if ($user_data != null) {
			$user = new User($this->w);
			$user->fill($user_data);
			$user->updateLastLogin();
			$this->w->session('user_id',$user->id);
			$this->w->session('timezone',$client_timezone);
			return $user;
		} else {
			return null;
		}
	}

	function loginLocalUser() {

	}

	function __init() {
		$this->_loadRoles();
	}

	function loggedIn() {
		return $this->w->session('user_id');
	}

	function getUserForLogin($login) {
		return $this->getObject("User", array("login",$login));
	}
	
	function & user() {
		if (!$this->_user && $this->loggedIn()) {
			$this->_user = $this->getObject("User", $this->w->session('user_id'));
		}
		return $this->_user;
	}

	/**
	 * 
	 * checks if the CURRENT user has this role
	 */
	function hasRole($role) {
		return $this->user() ? $this->user()->hasRole($role) : false;
	}
	
	function allowed($path,$url=null) {
		$p =explode("/", $path);
		$module = $p[0];
		$hsplit = explode("-",$module);
		$module = array_shift($hsplit);
		if (!in_array($module, $this->w->modules())) {
			return false;
		}
		if ($this->user()) {
			if ($this->user()->allowed($this->w,$path)) {
				return $url ? $url : true;
			}
		} else {
			return function_exists("anonymous_allowed") && anonymous_allowed($this->w,$path);
		}
		return false;
	}

	function getAllRoles() {
		$this->_loadRoles();
		if (!$this->_roles) {
			$roles = array();

			$funcs = get_defined_functions();
			foreach ($funcs['user'] as $f) {
				if (preg_match("/^role_(.+)_allowed$/", $f, $matches)) {
					$roles[]=$matches[1];
				}
			}
			$this->_roles = $roles;
		}
		return $this->_roles;
	}

	function _loadRoles() {
		// do this only once
		if ($this->_roles_loaded)
		return;

		$modules = $this->w->modules();
		foreach ($modules as $model) {
			$file = $this->w->getModuleDir($model).$model.".roles.php";
			if (file_exists($file)) {
				require_once $file;
			}
		}
		$this->_roles_loaded = true;
	}

	function & getUser($id) {
		return $this->getObject("User", $id);
	}

	function & getUsers($includeDeleted = false) {
		return $this->getObjects("User",array('is_deleted', $includeDeleted ? 1 : 0),true);
	}

	function & getUserForContact($cid) {
		return $this->getObject("User", array("contact_id",$cid));
	}

	function & getUsersForRole($role) {
		if (!$role) {
			return null;
		}
		$users = $this->getUsers();
		$roleUsers = array();
		if ($users) {
			foreach ($users as $u) {
				if ($u->hasRole($role)) {
					$roleUsers[] = $u;
				}
			}
		}
		return $roleUsers;
	}

	function & getGroups()
	{
		$rows = $this->_db->get("user")->where(array('is_active'=>1,'is_deleted'=>0,'is_group'=>1))->fetch_all();
		 
		if ($rows)
		{
			$objects = $this->fillObjects("User", $rows);

			return $objects;
		}
		return null;
	}

	function getGroupMembers($group_id = null, $user_id = null)
	{
		if ($group_id)
		$option['group_id'] = $group_id;
		 
		if ($user_id)
		$option['user_id'] = $user_id;
		 
		$groupMembers = $this->getObjects("GroupUser", $option, true);
		 
		if ($groupMembers)
		{
			return $groupMembers;
		}
		return null;
	}

	function getGroupMemberById($id)
	{
		$groupMember = $this->getObject("GroupUser", $id);
		 
		if ($groupMember)
		{
			return $groupMember;
		}
		return null;
	}

	function getRoleForLoginUser($group_id, $user_id)
	{
		$groupMember = $this->getObject("GroupUser", array('group_id'=>$group_id,'user_id'=>$user_id));

		if ($groupMember)
		{
			return $groupMember->role;
		}
		return null;
	}
}
