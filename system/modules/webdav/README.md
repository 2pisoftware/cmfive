#Webdav module

## Summary
The webdav module for CmFive provides access to Attachment records stored in the system using a Webdav filesystem client.

The filesystem tree reflects the objects that contain the attachments filtered according to access for the authenticated user.
[Not all objects implement the can* methods eg User, Attachment]
Currently implemented User, Wiki, TaskGroup and Task, Tag.

The Cmfive file API is used to source attachments seamlessly across distributed file systems.

eg
DB
	TaskGroups
		Dev Team 1
			Files
				contract.txt
			Tasks
				README.txt
				screen.png
			
The tree is not editable but the files inside are.

By default, Attachments can be associated with any Object regardless of the Cmfive UI.

## Install
[Apply the webdavsupport cmfive branch]

- Checkout the webdav module `git clone https://github.com/syntithenai/webdav.git`
- Link the module into the site modules folder `ln -s /home/projects/webdav /var/www/cmfive/modules/webdav`
- Update composer using the cmfive tool. The sabre dependancy is defined in the  module.
- Apply the migrations using the cmfive admin migration tool.
- Ensure CSRF is disabled and the module is allowed access without authentication.
`Config::set("system.checkCSRF", false);

Config::set("system.csrf.enabled", false);

Config::set("system.allow_module", array(
     "rest", // uncomment this to switch on REST access to the database objects. Tread with CAUTION!
     "webdav"
)); 

`

- Use the admin or profile tools to update the user passwords so that the password digest can be written.


## Mapping a drive in windows

- Go to My Computer
- Click Map Network Drive
- Click the link "Connect to a website that you can use to store you documents or pictures"
- Click Next, Click Next
- Fill the URL for your webdav server `http://cmfivesite.com/webdav` and press enter.


## Config
The config.php file for the module controls what objects appear at the root of the filesystem.

`Config::set('webdav', array(
	......
	'filesystems' => ['','/uploads/attachments'],  // relative to ROOT_PATH
	'availableObjects' => ['Wiki'=>[],'TaskGroup'=>[],'Task'=>[],'User'=>[] ],
));`



## Technical
The sabre/dav library is used to implement the webdav protocol.

### Single action
A single cmfive action is available the /webdav/webdav.actions.php which includes the function default_ALL().
[A patch to core is required to support default/fallback action for a module]


### Classes

- DBRootINode - root container with configured list of available objects as children
- ClassInode - container for an Object which lists all records inside.
- DBObjectINode - container for a record which lists all attachments inside and potentially other related records.
- AttachmentInode - file
- WebdavAuthentication - cmfive integration plugin
- WikiINode - DBObjectINode extension adds wiki pages as children
- WikiPageINode - plain DBObjectINode extension
- TaskGroupINode - DBObjectINode extension adds tasks as children
- TaskINode - plain DBObjectINode extension
- UserINode - plain DBObjectINode extension
- INodeService - shared service class

To enable additional record types to be displayed via webdav, create a new INode class with name matching the object to be mapped ie MedicalRecordINode that extends DBObjectINode and then override appropriate methods.


## Authentication
Cmfive based authentication is implemented by extending a Cmfive to implement digest authentication.

The AuthUserPasswordDigest migration adds the password_digest field to the database.
[** TODO find a solution for adding the field to the user object or swapping out the user object. Currently hacked digest field into User.php]

webdav.hooks.php updates the digest field when a user password is saved.
[requiring an auth_setpassword hook to be added to User.php]

*User passwords must be updated in the UI to enable them for webdav. *

WebdavAuthentication.php implements the correct functions to be provided to the sabre webdav server as an authentication plugin.

Objects are filtered according to the canView,canList,canEdit,canDelete functions for the logged in user.

