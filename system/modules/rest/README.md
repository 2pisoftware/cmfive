#REST module

The rest module implements an HTTP interface to generic CRUD persistence. It can be used to search, save and delete records.

It is particularly useful in the development of ajax applications to load data subsets in the background.

CmFive object based access controls canEdit, canDelete, canView are respected by requests to the API.

Validation rules on CmFive objects are supported and the save endpoint returns the errors as JSON.

By implementing content that may need to be refreshed without triggering a full page reload as partials, the content can be injected directly into other templates using the $w->partial function and the same content can be accessed without the rest of the page using the rest api.

Partial rendering is supported in two ways
- Using the /rest/partial/<module>/<classname>/<partial> endpoint that directly renders the partial with no parameters.
- Using the /rest/searchpartial/<module>/<classname>/<partial>/[QUERY] endpoint that searches for objects based on the QUERY part of the url and makes them available to the partial template in $w->ctx('results')


##QuickStart

1. Configure the rest module in the global config file so that the object you want included is in the system.rest_allow array. 
>		// use the API_KEY to authenticate with username and password
>		Config::set('system.rest_api_key', "abcdefghijklmnopqrstuv");
>		// exclude any objects that you do NOT want available via REST
>		// note: only DbObjects which have the $_rest; property are 
>		// accessible via REST anyway!
>		Config::set('system.rest_allow', array(
>			"User",
>			"Contact"
>		));

2. To use the rest api in your module you will need a rest authentication token which can be retrieved from the by sending a GET request to 
`/rest/token/?api=<apikey>[&username=<username>&password=<password>]`

Username and password are not required if there is already a logged in user

This token needs to be appended to every subsequent request.

>		$.ajax(
>			"/rest/token?apikey=<?php echo Config::get("system.rest_api_key") ?>",
>			{cache: false,dataType: "json"}
>		).done(function(token) {
>		  // make requests here
>		});

3. Then, make requests to the API.
>		// SEARCH
>		$.ajax(
>			"/rest/index/WikiPage?token=" + token,
>			{cache: false,dataType: "json"}
>		).done(function(token) {
>			if (response.success && response.success.length > 0) {
>				// we got results
>				console.log(response.success);
>			}
>		});
>		// SAVE/DELETE
>		$.ajax(
>			"/rest/save/WikiPage?token=" + token,
>			// OR "/rest/delete/WikiPage?token=" + token,
>			var data={id: '<?php echo $obj->id ?>',body: '<?php echo $obj->body ?>'}
>			{cache: false,dataType: "json",method:"POST"}
>		).done(function(token) {
>			if (response.success > 0) {
>				// we got a result containing the updated record
>				console.log(response.success);
>			}
>		});


## REST API

- [] indicates an optional value
- <> indicate a required value

----------------------------------------------

To get a single record send a GET request to 

/rest/index/<classname>/id/<id>?token=<authtoken>

----------------------------------------------

To search/retrieve multiple records send a GET request to 

/rest/index/<classname>/[fieldname]/[value]?token=<authtoken>

Fields marked deleted are excluded from the list request, to access all records regardless of deleted status use the deleted request.

/rest/deleted/<classname>/[fieldname]/[value]?token=<authtoken>

Both index and delete support advanced search criteria ie /rest/index/<classname>/<advanced criteria>?token=<authtoken>
where <advancecriteria> works as follows
- if it starts with /SKIP/<integer> search results are skipped according to the parameter
- if it starts with or skip is followed by /LIMIT/<integer> a limit is applied to the number of search results (by default 10)
- if AND or OR is found, a query group is created
	- END closes as sub group
	- otherwise config pairs are processed as <field__operator>, <data1__data2> until AND or OR is found or the end of the configuration
	- if AND or OR is found again, a sub query group is created
- if criteria pairs are found before AND or OR, the default condition is AND
eg
# fred and age 4-60
/LIMIT/10/name___like/fred/age___between/40___60
# freds aged 0-20,80+ or jill
/SKIP/10/LIMIT/10/AND/name___like/fred/OR/age__between/0___20/age___greater/80/END/name___like/jill
	 
----------------------------------------------

To delete records, send a POST request to 

/rest/delete/<classname>/[id]?token=<authtoken>

----------------------------------------------

To save records POST record data to 

/rest/save/<classname>/?token=<authtoken>
-----------------------------------------------

To request HTML rendered from a partial send a GET request to

/rest/partial/<module>/<classname>/<partial>?token=<authtoken>
eg
/rest/partial/favorite/Favorite/listfavorite?token=567890
-----------------------------------------------

To request HTML rendered from a partial that sources data from the REST URL query send a GET request to

/rest/searchpartial/<module>/<classname>/<partial>/[query as per index]?token=<authtoken>
eg
/rest/searchpartial/favorite/Favorite/listfavorite/name___like/fred?token=567890


