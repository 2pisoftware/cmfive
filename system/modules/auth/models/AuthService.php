<?php

class AuthService extends DbService {

	public $_roles;
	public $_roles_loaded = false;
	public $_user = null;
	public $_rest_user = null;

	function login($login, $password, $client_timezone, $skip_session = false) {
		// $password = User::encryptPassword($password);
		// $user_data = $this->_db->get("user")->where("login", $login)->and("password", $password)->and("is_active", "1")->and("is_deleted", "0")->fetch_row();
		$credentials['login']=$login;
		$credentials['password']=$password;
		//allow pre login hook for alternative authentications.
		//this hook returns $hook_results[$module]=$user or null.
		$hook_results = $this->w->callHook("auth", "prelogin", $credentials);
		foreach($hook_results as $module => $user) {
			//@TODO: check config for $module.optional or $module.manditory. default to optional for now. if any manditory returns null then return null.
			if (!empty($user)) {
				$this->w->Log->info($user->getFullName()." authenticated via ".$module." prelogin hook");
				break;
			} else {
				$this->w->Log->info('prelogin hook did not provide authentication: '.$login);
			}
		}
		
		//if no valid user check if credentials pass against cmfive user table
		//if so set user else abort.
		if (empty($user)) {
			$user = $this->getUserForLogin($login);
			if (empty($user)) {
				$this->w->Log->info('cmfive user does not exist: '.$login);
				return null;
			}
			if ($user->encryptPassword($password) !== $user->password) {
				$this->w->Log->info('cmfive pasword mismatch for username: '.$login);
				return null;
			}
		}
		$this->w->Log->info("User logged in: ".$user->getFullName());
		$hook_results = $this->w->callHook("auth", "postlogin", $user);
		/*foreach($hook_results as $module => $user) {
			//
		}*/

		$user->updateLastLogin();
		if (!$skip_session) {
			$this->w->session('user_id', $user->id);
			$this->w->session('timezone', $client_timezone);
		}
		return $user;
	}

	function forceLogin($user_id = null) {
		if (empty($user_id)) {
			return;
		}

		$user = $this->getUser($user_id);
		if (empty($user->id)) {
			return null;
		}

		$user->updateLastLogin();
		$this->w->session('user_id', $user->id);
	}

	function __init() {
		$this->_loadRoles();
	}

	function loggedIn() {
		return $this->w->session('user_id');
	}

	function getUserForLogin($login) {
		$user = $this->db->get("user")->where("login COLLATE utf8_bin = ?", $login)->fetch_row();
		$user_obj = $this->getObjectFromRow("User", $user);
		return $user_obj;
	}

	function getUserForToken($token) {
		return $this->getObject("User", array("password_reset_token" => $token));
	}

	function setRestUser($user) {
		$this->_rest_user = $user;
	}

	function user() {
		// special case where RestService handles authentication
		if ($this->_rest_user) {
			return $this->_rest_user;
		}
		// normal session based authentication
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

	function allowed($path, $url = null) {
		$parts = $this->w->parseUrl($path);
		if (!in_array($parts['module'], $this->w->modules())) {
			$this->Log->error("Denied access: module '". urlencode($parts['module']). "' doesn't exist");
			return false;
		}

		if ((function_exists("anonymous_allowed") && anonymous_allowed($this->w, $path)) || ($this->user() && $this->user()->allowed($path))) {
			return $url ? $url : true;
		}
		
		if (empty($this->user()) && (Config::get('system.use_passthrough_authentication') === TRUE) && !empty($_SERVER['AUTH_USER'])) {
			// Get the username
			$username = explode('\\', $_SERVER["AUTH_USER"]);
			$username = end($username);
			$this->w->Log->debug("Passthrough Username: " . $username);
			
			//this hook returns $hook_results[$module][0]=$user or null.
			$hook_results = $this->w->callHook("auth", "get_user_for_passthrough", $username);
			foreach($hook_results as $module => $user) {
				
				if (!empty($user) && $user instanceof User) {
					$this->forceLogin($user->id);
					if ($user->allowed($path)) {
						return $url ? $url : true;
					}
				} else {
					$this->Log->info($module.' did not provide passthrough user for:'.$username);
				}
			}
		}
		
		return false;
	}

	/**
	 * Return an array of role names for all available roles
	 *
	 * @return array of strings
	 */
	function getAllRoles() {
		$this->_loadRoles();
		if (!$this->_roles) {
			$roles = array();

			$funcs = get_defined_functions();
			foreach ($funcs['user'] as $f) {
				if (preg_match("/^role_(.+)_allowed$/", $f, $matches)) {
					$roles[] = $matches[1];
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
			$file = $this->w->getModuleDir($model) . $model . ".roles.php";
			if (file_exists($file)) {
				require_once $file;
			}
		}
		$this->_roles_loaded = true;
	}

	function getUser($id) {
		return $this->getObject("User", $id);
	}

	function getUsersAndGroups($includeDeleted = false) {
		$where = array();
		if (!$includeDeleted) {
			$where["is_deleted"]=0;
		}
		return $this->getObjects("User", $where, true);
	}

	function getUsers($includeDeleted = false) {
		$where = array();
		$where["is_group"]=0;
		if (!$includeDeleted) {
			$where["is_deleted"]=0;
		}
		return $this->getObjects("User", $where, true);
	}

	function getUserForContact($cid) {
		return $this->getObject("User", array("contact_id" => $cid));
	}

	function getUsersForRole($role) {
		if (!$role) {
			return null;
		}
		$users = $this->getUsersAndGroups();
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

	function getGroups() {
		$rows = $this->_db->get("user")->where(array('is_active' => 1, 'is_deleted' => 0, 'is_group' => 1))->fetch_all();

		if ($rows) {
			$objects = $this->fillObjects("User", $rows);

			return $objects;
		}
		return null;
	}

	function getGroupMembers($group_id = null, $user_id = null) {
		if ($group_id)
		$option['group_id'] = $group_id;

		if ($user_id)
		$option['user_id'] = $user_id;

		$groupMembers = $this->getObjects("GroupUser", $option, true);

		if ($groupMembers) {
			return $groupMembers;
		}
		return null;
	}

	function getGroupMemberById($id) {
		$groupMember = $this->getObject("GroupUser", $id);

		if ($groupMember) {
			return $groupMember;
		}
		return null;
	}

	function getRoleForLoginUser($group_id, $user_id) {
		$groupMember = $this->getObject("GroupUser", array('group_id' => $group_id, 'user_id' => $user_id));

		if ($groupMember) {
			return $groupMember->role;
		}
		return null;
	}

}
