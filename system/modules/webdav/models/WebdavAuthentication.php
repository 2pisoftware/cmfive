<?php
use Sabre\DAV;

/**
 * Authentication plugin for cmfive
 */
class WebdavAuthentication extends  Sabre\DAV\Auth\Backend\AbstractDigest {
//class WebdavAuthentication extends  Sabre\DAV\Auth\Backend\AbstractBasic {
	
	private $w;
	
	function __construct($w) {
		$this->w=$w;
		$webdavConfig=Config::get('webdav');
		$realm='';
		if (array_key_exists('authenticationRealm',$webdavConfig)) {
			$realm=$webdavConfig['authenticationRealm'];
		}
		if (empty($realm)) {
			$realm='CmFive';
		}
        $this->setRealm($realm);
	}
	
	/**
	 *  For Basic Auth - doesn't work with windows 
	 */
	function validateUserPass($user,$password) {
		return $this->w->Auth->login($user,$password,'GMT');
	}
	
	/**
	 * For digest auth
	 * Returns the digest hash for a user.
	 *
	 * @param string $realm
	 * @param string $username
	 * @return string|null
	 */
	function getDigestHash($realm, $username) {
		$user=$this->w->Auth->getUserForLogin($username);
		if (!empty($user)) {
			return $user->password_digest;
		}
	}
	 
	
	 /**
	  * Force CmFive login on successful auth 
	 * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return array
     */
    function check(Sabre\HTTP\RequestInterface $request, Sabre\HTTP\ResponseInterface $response) {
		$result=parent::check($request,$response);
		if ($result[0]==true) {
			$username=substr($result[1],strlen($this->principalPrefix));
			$user=$this->w->Auth->getUserForLogin($username);
			$this->w->Auth->forceLogin($user->id);
		}
		return $result;
	}
	
    
}
