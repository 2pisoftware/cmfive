<?php
use Sabre\DAV;

/**
 * Catch all action for request into the webdav module
 */
function default_ALL(Web &$w) {	
	$w->setLayout(null);
	
	// Now we're creating a whole bunch of objects
	//$rootDirectory = new DAV\FS\Directory(ROOT_PATH);
	$rootDirectory = new DBRootINode($w);
	// The server object is responsible for making sense out of the WebDAV protocol
	$server = new Sabre\DAV\Server($rootDirectory);

	// If your server is not on your webroot, make sure the following line has the
	// correct information
	$server->setBaseUri('/webdav');
	
	// The lock manager is reponsible for making sure users don't overwrite
	// each others changes.
	$lockBackend = new DAV\Locks\Backend\File(ROOT_PATH.'/cache/filelocks');
	$lockPlugin = new DAV\Locks\Plugin($lockBackend);
	$server->addPlugin($lockPlugin);

	// This ensures that we get a pretty index in the browser, but it is
	// optional.
	$server->addPlugin(new Sabre\DAV\Browser\Plugin());
	
	// auto content types
	$server->addPlugin(new \Sabre\DAV\Browser\GuessContentType());
	
	// cmfive authentication
	$authBackend = new WebdavAuthentication($w);
	$authPlugin = new Sabre\DAV\Auth\Plugin($authBackend);
	$server->addPlugin($authPlugin);

	// property storage plugin
	$storageBackend = new Sabre\DAV\PropertyStorage\Backend\PDO($w->db);
	$propertyStorage = new \Sabre\DAV\PropertyStorage\Plugin($storageBackend);
	$server->addPlugin($propertyStorage);
	
	//try {
	// All we need to do now, is to fire up the server
		$server->exec();
	//} catch (Exception $e) {
	//	echo $e->getMessage();
	//	die();
	//}
	
	
}


