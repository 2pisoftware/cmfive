# Translations in CmFive

## Quickstart

Edit your user profile to set a language.

As a developer, 

- ensure all text strings are wrapped with global translation functions.
- update the .po files and compile to .mo files to provide translations for any additional strings.
- restart your web server to clear .mo file cache


## Overview
Cmfive uses a gettext based approach to translations inspired by WordPress.

Global functions are used to wrap all text strings in the application. (See functions.php)


`__($k,$context,$domain) 			- returns a translated value for $k in $domain
_e($k,$context,$domain) 			- echos the translated value 
_n($k1,$k2,$count,$context,$domain) 	- return plural form of translated value
								- $k1 is the singular value, $k2 is the plural value
_en($k1,$k2,$count,$context,$domain) 	- echo plural form

These functions can be identified in source code by tools like poedit to prepare and maintain .po files ready for translation.
Poedit can also be used to compile the language files into binary .mo format.

Note that 
- the $context parameter is optional and may be used to refine the translation based on context.
- the $domain parameter is optional and may be used to explicitly refer to a translation in another module than would be automatically determined by source code as described below in the actions of the Web class.

### The web class 

- sets locale for the current user on initialisation.  (see $web->initLocale())
- sets the current translation lookup domain to match the name of the current module.  (see $web->setTranslationDomain($domain))
- when using a hook or partial
	- sets the current translation lookup domain to match the hook or partial module
	- restores the current translation lookup at the completion of hook or partial

Where translations are not available for a module in the current language, the system will attempt to fall back to the `main` module for translations.
	


### Translations file system
Each module contains a folder tree of translations structured as
translations
	de_DE
		LC_MESSAGES
			moduleName.po # editable translation file
			moduleName.mo # compiled translation file.
	...

Available language include english, german, french, italian, irish, dutch, russian, japanese and chinese (See User->getAvailableLanguages). 

To deploying additional languages 
- the operating system must provide or generate locales for all available languages
	- Use `locale -a`
	- Use `locale-gen de_DE.UTF-8` to generate german locale.
	- the cmfive docker image ensures that all enabled locales are generated
- User->getAvailableLanguages must be updated to include the key for UI selection
- translation files must be provide for every module as described above.


## Translation String Extraction

Translation strings can be extracted by poedit or xgettext.

Both tools need to be informed about the names and parameters of the global functions.

`xgettext --language=PHP --add-comments=L10N --keyword=__:1 --keyword=__:1,2c --keyword=_n:1,2 --keyword=_n:1,2,4c  file.php`
           
for poedit add rules as
__:1				- plain
__:1,2c				- with context
_e:1				- plain
_e:1,2c				- with context
_n:1,2				- plural
_n:1,2,4c			- plural with context
_en:1,2				- plural
_en:1,2,4c			- plural with context


A comment in code IMMEDIATELY proceeding a call to a gettext lookup function is added as a comment to the the translation.
Both /**/ and // style comments are supported.
The xgettext binary supports filtering by using the --add-comments parameter that requires that comments start with the specified string. By default, poedit uses  --add-comments=TRANSLATORS: as an argument to xgettext. This can be changed in preference for each file type.


## Notes

- The translations have been completed using https://poeditor.com/. The cmfive translation project has been accredited as open source so we can store, manage and collaborate on translations indefinately for free.
- Currently all the translations are machine translations using google.
- There are currently no translations for russian and chinese.
- FOR NOW, the system/main module is the only one with translations. This is to simplify the process of managing translation files during development. Once we are ready to deploy, each module will have it's own translation file.
- need to revisit plurals - missed a few need to update to inject count into translatable string so order can be rearranged by translaters if needed.  eg `_n(' %d task',' %d tasks',2)`
-  With a few exceptions, there is no html in the translation strings
- All the likely templates have been updated to add meta=UTF-8 and remove any other encoding.
	- **??There is something strange with template selection - currently rendering admin/templates/index.tpl.html as master template ??**
- .mo files are cache by the webserver. A webserver restart (or in my case a docker machine restart)

## Issues Remaining
- Ensuring completion of translation markers. Also in static resource files eg JS
- Human translations. We will certainly need to add context to translation markers as human translators require more explicit control.
- The tasks module has text strings that are loaded into config (and so cached regardless of user language settings)
- Staticly declared class variables using cannot use the global translation functions. (See FormStandardInterface)
- The Lookup table could be extended to allow for language keys ??
- Text strings in javascript and other resource files are not currently translated. Wordpress provides a wrapper for reading and replacing language markers in resource files that could be implemented here. Alternatively js file could be renamed as .js.php files and use php directly to inject translations.
- User roles are displayed as their raw key value. When listing roles, the list is dynamically generated.
	- In general it is bad practice to include variables in translation strings. In this case I think it makes sense to provide translations for all the roles and call the global translation replacement functions with the role key variable. !!TODO
- Dates
	- there are a handful of functions that format dates according a default - formatDate, formatDateTime, ...   These function can be easily localised by removing the explicit default format. Setting of locale is already implemented.
	- There are also many instances of directly using the date function to render a particular date format. These various formats need to be refactored into named date format functions that can be localised.
- Currency
- Help Files 
	- the help system is slated for upgrades in the immediate future. Part of the upgrades will be allowance for multiple help files with a language key in their filename.
- Business translation - initial translation of lookup strings into other lookup strings for business labelling/branding


## test code
	// single
	$w->setTranslationDomain('report');
	echo __('chair')."<br>";
	// TRANSLATORS: THIS IS A KITCHEN CHAIR
	echo __('chair','kitchen')."<br>";
	echo __('chair','kitchen','main')."<br>";
	echo __('chair','','main')."<br>";
	_e('freezer');
	echo "<br>";
	//$w->setTranslationDomain('main');
	echo __('chair','kitchen')."<br>";
	echo __('chair','kitchen','main')."<br>";
	echo __('chair','','main')."<br>";
	_e('freezer');
	echo "<br>";
	
	
	// plural
	echo _n('%d cat ','%d cats',1)."<br>";
	echo _n('%d frog ','%d frogs',2)."<br>";
	_en('%d dog','%d dogs',2);
	echo "<br>";
	// plural with context
	echo _n('%d frog ','%d smelly froggies',2,"french people")."<br>";
	
	die();
	
	
	
