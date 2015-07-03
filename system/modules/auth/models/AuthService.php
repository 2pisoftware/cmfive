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
		//this hook returns $hook_results[$module] and $hook_results[0]=$user or null. 
		$hook_results = $this->w->callHook("core_auth", "prelogin", $credentials);
		foreach($hook_results as $module => $hook_result) {
			//@TODO: check config for $module.optional or $module.manditory. default to optional for now.
			$user = $hook_result[0];
			if (!empty($user)) {
				break;
			}
		}

		//check if credentials pass against cmfive user table
		//if so set user else abort.
		if (empty($user)) {
			echo "empty user";
			$user = $this->getUserForLogin($login);
			if ($user->encryptPassword($password) !== $user->password) {
				return null;
			}
		}
		

		// if ($user_data != null) {
		// $user = new User($this->w);
		// $user->fill($user_data);
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
		$user = $this->db->get("user")->where("login", $login)->fetch_row();
		$user_obj = $this->getObjectFromRow("User", $user);
		// Could someone tell me why getObject instantly returns "admin" and not the user im after?

		// var_dump($user);
		// echo $login;
		// $result = $this->getObject("User", array("login", $login));
		// echo $result->login; die();

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

		// First, check for Windows pass through auth
		if (Config::get('system.use_passthrough_authentication') === TRUE) {
			if (!empty($_SERVER['AUTH_USER']) && !$this->loggedIn()) {
				// Get the username
				$username = explode('\\', $_SERVER["AUTH_USER"]);
				$username = end($username);

				$this->w->Log->debug("Username: " . $username);

				// Authenticate agaianst LDAP
				$ldap = ldap_connect(Config::get("system.ldap.host"), Config::get("system.ldap.port"));

				if (!$ldap) {
					$this->w->Log->error("LDAP Server could not be reached");
					return false;
				}

				ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3); // Recommended for AD
				ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

				//Using the provided user and password to login into LDAP server.
				//For the dc, normally will be the domain.
				$ldap_instance = ldap_bind($ldap, Config::get("system.ldap.username"), Config::get("system.ldap.password"));

				// You may add in any filter part on here. "uid" is a profile data inside the LDAP. You may filter by other columns depends on your LDAP setup.
				$search_results = ldap_search($ldap, Config::get("system.ldap.base_dn"),
				str_replace("{username}", $username, Config::get("system.ldap.auth_search")), Config::get("system.ldap.search_filter_attribute"), 0, 100);

				$info = ldap_get_entries($ldap, $search_results);

				// var_dump($info); die();

				if ($info['count'] == 0) {
					$this->w->Log->error("LDAP Info: " . json_encode($info));
					$this->w->Log->error("LDAP Error: " . ldap_error($ldap));
					return false;
				}

				// Close LDAP connection
				ldap_close($ldap);

				// Allow module based validation via hooks
				$hook_results = $this->w->callHook("core_auth", "ldap_authenticate", $info);
				foreach($hook_results as $hook_result) {
					if ($hook_result === FALSE) {
						return false;
					}
				}

				// Try and find the user locally
				$user = $this->getObject("User", array("login" => $username));
				if (empty($user)) {
					$contact = new Contact($this->w);
					$contact->firstname = !empty($info[0]["givenname"][0]) ? $info[0]["givenname"][0] : $username;
					$contact->lastname = !empty($info[0]["sn"][0]) ? $info[0]["sn"][0] : '';
					$contact->insert();

					// Create a user
					$user = new User($this->w);
					$user->login = $username;
					// Set password if provided
					$user->setPassword($_SERVER['AUTH_PASSWORD']);
					$user->contact_id = $contact->id;
					$user->is_admin = 0;
					$user->is_active = 1;
					$user->insert();

					$user->addRole("user");
					$user->is_admin = 1;
					$user->update();
				} else {
					$contact=$user->getContact();
					$contact->firstname = !empty($info[0]["givenname"][0]) ? $info[0]["givenname"][0] : $username;
					$contact->lastname = !empty($info[0]["sn"][0]) ? $info[0]["sn"][0] : '';
					$contact->update();
				}

				$this->forceLogin($user->id);

				// Let modules know that a user successfully authenticated
				$this->w->callHook("core_auth", "ldap_user_authenticated", $user);

				// Here is where we introduce LDAP support and check against windows server user records for access
				// For testing, allow everything
				if (!empty($user->id)) {
					return $url ? $url : true;
				}
			} else {
				if ($this->loggedIn())  {
					return $url ? $url : true;
				} else {
					return false;
				}
			}
		}
		if (Config::get('system.use_passthrough_authentication') === TRUE) {
			// Allow module based validation via hooks
			$hook_results = $this->w->callHook("core_auth", "prelogin", $info);
			foreach($hook_results as $hook_result) {
				if ($hook_result === FALSE) {
					return false;
				}
			}
		}
			


		if ((function_exists("anonymous_allowed") && anonymous_allowed($this->w, $path)) ||
		($this->user() && $this->user()->allowed($path))) {
			return $url ? $url : true;
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
