
var testsRunning=false;
// prevent close window losing test results
//window.onbeforeunload = function() {
//    return 'You will lose your test results!';
//}
window.onerror = function(message, url, lineNumber) {  
  //save error and send to server for example.
  console.log(message,url,lineNumber);
  return true;
};

function initialisePage() {
	//localStorage.getItem('enableTesting');
	if ($('.testnamewarning').length>0) {
		$('#runbutton').remove();
		$('.accordion').remove();
	}
	
	var activeSuite=localStorage.getItem('activeTestSuite');
	if (typeof activeSuite=="string" && activeSuite.length>0) {
		$('.accordion-navigation div.content').removeClass('active');
		$('.accordion-navigation[data-suite="'+activeSuite+'"] div.content').addClass('active');
	}
	$(document).foundation(); // {'reveal': {'close_on_background_click': true,'close_on_esc': true}}); {'reveal': {'close_on_background_click': true,'close_on_esc': true}});
	$('#testsenabled').change(function() {
		if (this.checked) {
			localStorage.setItem('enableTesting','1');	
			$('#warning').hide();
		} else {
			localStorage.setItem('enableTesting','0');	
		}
	});
	if (localStorage.getItem('enableTesting')!='1')  {
		$('#warning').show();
	} else {
		$('#testsenabled')[0].checked=true;
	}
	$('#selectallbutton').click(function() {
		$('.suiteselected').prop('checked', true);
		$('.testselected').prop('checked', true);
	});
	$('#selectnonebutton').click(function() {
		$('.suiteselected').prop('checked', false);
		$('.testselected').prop('checked', false);
	});
	$('#selectfailedbutton').click(function() {
		$('.suiteselected').prop('checked', false);
		$('.testselected').prop('checked', false);
		$.each($('.testselected'),function(k,v) {
			if ($(v).parent().hasClass('testresult-failed')) $(v).prop('checked', true);
		});
		$('.suiteselected').each(function(k,v) {
			var suite=$(this).parents('li.testsuite').data('suite');
			if (countAllTests(suite)==countSelectedTests(suite)) {
				$(this).parents('li.testsuite').children('.suiteselected')[0].checked=true; //.prop('checked','true');
			} else {
				$(this).parents('li.testsuite').children('.suiteselected')[0].checked=false; //.prop('checked','false');
			}
		});
	});
	$('#selectpendingbutton').click(function() {
		$('.suiteselected').prop('checked', false);
		$('.testselected').prop('checked', false);
		$.each($('.testselected'),function(k,v) {
			if ($(v).parent().hasClass('testresult-pending')) {
				$(v).prop('checked', true);
			}
		});
		$('.suiteselected').each(function(k,v) {
			var suite=$(this).parents('li.testsuite').data('suite');
			if (countAllTests(suite)==countSelectedTests(suite)) {
				$(this).parents('li.testsuite').children('.suiteselected')[0].checked=true; //.prop('checked','true');
			} else {
				$(this).parents('li.testsuite').children('.suiteselected')[0].checked=false; //.prop('checked','false');
			}
		});
		
	});
	// toggle all test selections on suite selection
	$('.suiteselected').change(function() {
		$('.testselected',$(this).parent()).prop("checked",$(this).prop("checked"));
	});
	
	// toggle suite selection where all contained tests are selected
	$('.testselected').change(function() {
		var suite=$(this).parents('li.testsuite').data('suite');
		if (countAllTests(suite)==countSelectedTests(suite)) {
			$(this).parents('li.testsuite').children('.suiteselected')[0].checked=true; //.prop('checked','true');
		} else {
			$(this).parents('li.testsuite').children('.suiteselected')[0].checked=false; //.prop('checked','false');
		}
	});
	
	$.get('dbmanager.php?checkmysqldiffs=1&mini=1'+getParams(),function(res) {
			if (res && res.length>0) {
				$('#showdbtools b').text(res);
			} else {
				$('#showdbtools b').text('');
			}
			
	});
	
	var xhr ;
	
	
	function startTests(tests) {
		if ($('#testsenabled:checked').length>0) {
			UIStartTests();
			$('.testdetailsreveal').html('');
			xhr= new XMLHttpRequest();
			var lastContent='';
			var currentContent='';
			var locationParts=document.location.href.split("/");
			if (tests.indexOf('?')==-1) {
				tests+='?';
			} else {
				tests+='&';
			}
			var keyid=$('#md5keyid').val();
			var key=$('#md5key').val();
			tests+='key='+key+'&keyid='+keyid;
			var testLocation=locationParts.slice(0,locationParts.length-1).join("/")+'/'+tests;
			xhr.open("GET", testLocation, true);
			xhr.onprogress = function(e) {
				var lastContent=currentContent;
				currentContent=e.currentTarget.responseText;
				newContent=currentContent.substr(lastContent.length);
				try {
					updatePage(newContent);
				} catch (e) {}
			}
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4) {
					UIStopTests();
				}
			}
			xhr.send();
		} else {
			alert('Confirm that you understand before running tests.');
		}
	}
	function getParams() {
		var keyid=$('#md5keyid').val();
		var key=$('#md5key').val();
		return '&key='+key+'&keyid='+keyid;
		
	}
	function flashDialog(name,content) {
		$('#'+name).remove();
		$('body').append($('<div id="'+name+'" class="reveal-modal" data-reveal aria-hidden="true" role="dialog">'+content+'</div>'));
		$('#'+name).foundation('reveal','open');
		setTimeout(function() {
			$('#'+name).foundation('reveal','close');
		},2000);	
	}
	
	$('#showdbtools').click(function() {
		$('#dbtools').html("<div id='mysqldiffs' class='right' ></div><div id='savesnapshot' ><a href='#' class='button tiny' id='savesnapshotbutton' >Save Database Snapshot</a></div><div id='loadsnapshot' ><a href='#' class='button tiny' id='loadsnapshotbutton' >Load Database Snapshot</a></div><div id='resetdatabases' ><a href='#' class='button tiny' id='resetalldatabasesbutton' >Drop and recreate all tables !!</a></div>");
		$('#mysqldiffs').load('dbmanager.php?checkmysqldiffs=1'+getParams(),function() {
			$('#listmysqldiffsbutton').click(function() {
				$('#dbtools').load('dbmanager.php?listmysqldiffs=1'+getParams());
			});
			$('#runmysqldiffsbutton').click(function() {
				if (confirm('Are you really sure that you want to update schema to match source code?')) {
					$('#mysqldiffs').load('dbmanager.php?runmysqldiffs=1'+getParams());
				}
			});
			$('#resetalldatabasesbutton').click(function() {
				if ($('#testsenabled:checked').length>0) {
					if (confirm('Are you really sure that you want to DROP ALL DATABASES and reimport schema?')) {
						$.get('dbmanager.php?resetsystemdatabases=1'+getParams(),function(res) {
							//$('#dbtools').foundation('reveal','close');
							flashDialog('resetallresult',res);
						});
					}
				} else {
					alert('Confirm that you understand before continuing.');
				}
				return false;
			});
			$('#savesnapshotbutton').click(function() {
				var saveAs=prompt('Save As?');
				if (saveAs.length>0) {
					$.get('dbmanager.php?savesnapshot='+saveAs+getParams(),function(res) {
						flashDialog('saved',res);
					});
				}
			});
			$('#loadsnapshotbutton').click(function() {
				var button=this;
				$('#snapshotlist').remove();
				$.get('dbmanager.php?listsnapshots=1'+getParams(),function(res) {		
					$('body').append($('<div id="snapshotlist" class="reveal-modal" data-reveal aria-hidden="true" role="dialog">'+res+'</div>'));
					//<a class="close-reveal-modal" aria-label="Close">&#215;</a>
					$('#snapshotlist').foundation('reveal','open');
					$('#snapshotlist .loadsnapshotbutton').click(function() {
						var saveAs=$(this).parent().data('filename');
						if (confirm('Really import database snapshot '+saveAs+' and DELETE EXISTING DATA ?')) { 
							$.get('dbmanager.php?loadsnapshot='+saveAs+getParams(),function(res) {
								flashDialog('loaded',res);
							});
						}
					});
					$('#snapshotlist .downloadsnapshotbutton').click(function() {
						var saveAs=$(this).parent().data('filename');
						var link=$('<a target="_new" href="'+'dbmanager.php?downloadsnapshot='+saveAs+getParams()+'" >eek</a>')
						$('body').append(link);
						link[0].click();
					});
					$('#snapshotlist .deletesnapshotbutton').click(function() {
						var saveAs=$(this).parent().data('filename');
						if (confirm('Really delete database snapshot '+saveAs +' ?')) { 
							var button=this;
							
							$.get('dbmanager.php?deletesnapshot='+saveAs+getParams(),function(res) {
								$(button).parent().remove();
							});
						}
					});
				});
			});
		});	
	});
	
	
	$('.accordion-navigation').click(function() {
		if ($(this).hasClass('active')) {
		} else {
			// open
			localStorage.setItem('activeTestSuite',$(this).data('suite'));
		}
	});
	$('.runtestbutton').click(function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		if (testsRunning==false)  {
			if (e.ctrlKey==true)  {
				window.open($(this).attr('href')+getParams()+'&v=1');
			} else {
				startTests($(this).attr('href')); //$(this).parent().attr('id'));
			}
		}
		return false;
	});
	$('.runtestsuitebutton').click(function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		if (testsRunning==false)  {
			if (e.ctrlKey==true)  {
				window.open($(this).attr('href')+getParams()+'&v=1');
			} else {
				startTests($(this).attr('href')); //$(this).parents('li').first().data('suite'));
			}
		}
		return false;
	});
    $('#runbutton').click(function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		if (testsRunning==false)  {
			if (e.ctrlKey==true)  {
				window.open('dbmanager.php?tests='+getSelectedTests()+getParams()+'&v=1');
			} else {
				startTests('dbmanager.php?tests='+getSelectedTests());
			}
		}
		return false;
	});
	$('#stopbutton').click(function() {
		if (testsRunning==true)  {
			UIStopTests();
			xhr.abort();
		}
	});
}
function UIStartTests() {
	testsRunning=true;
	$('#runbutton').hide();
	$('#stopbutton').show();
	$('body').addClass('disabled');
}
function UIStopTests() {
	testsRunning=false;
	$('#runbutton').show();
	$('#stopbutton').hide();
	$('body').removeClass('disabled');
}


function updateSuiteStatus(listedTest) {
	//check if all passed and update parent
	var suiteDOM=listedTest.parents('li.testsuite').first();
	var contentDOM=listedTest.parents('li.testsuite').find('div.content').first();
	var suite=suiteDOM.data('suite');
	var status=getSuiteStatus(suite);
	//console.log('update suites status'+status);
	//console.log('updatestatus',listedTest,suiteDOM,contentDOM,suite,status);
	if (status=='passed') {
		suiteDOM.removeClass('testresult-failed');
		suiteDOM.removeClass('testresult-pending');
		suiteDOM.addClass('testresult-passed');
		contentDOM.removeClass('testresult-failed');
		contentDOM.removeClass('testresult-pending');
		contentDOM.addClass('testresult-passed');
	} else if (status=='failed') {
		suiteDOM.removeClass('testresult-passed');
		suiteDOM.removeClass('testresult-pending');
		suiteDOM.addClass('testresult-failed');
		contentDOM.removeClass('testresult-passed');
		contentDOM.removeClass('testresult-pending');
		contentDOM.addClass('testresult-failed');	
	} else {
		suiteDOM.removeClass('testresult-failed');
		suiteDOM.removeClass('testresult-passed');
		suiteDOM.addClass('testresult-pending');
		contentDOM.removeClass('testresult-failed');
		contentDOM.removeClass('testresult-passed');
		contentDOM.addClass('testresult-pending');
	}
	updateTotalStatus();
}

function updateTotalStatus() {
	var all=$('li.testsuite').length;
	var failed=$('li.testsuite.testresult-failed').length;
	var passed=$('li.testsuite.testresult-passed').length;
	//console.log('update total status',passed,all);
	if (passed==all) {
		$('#runbutton').parent().removeClass('testresult-pending');
		$('#runbutton').parent().removeClass('testresult-failed');
		$('#runbutton').parent().addClass('testresult-passed');
	} else if (failed>0) {
		$('#runbutton').parent().removeClass('testresult-passed');
		$('#runbutton').parent().removeClass('testresult-pending');
		$('#runbutton').parent().addClass('testresult-failed');
	}
	
}

function updatePage(latestContent) {
	$('#phperrors').hide();
	$('#phperrors').html('');
	//console.log('updatepage',latestContent);
	var newContentWrapped=$('<div>'+latestContent+'</div>');
	//console.log('wrapped',newContentWrapped.html());
	$.each(newContentWrapped.children(),function(nk,newContentp) {
		var newContent=$(newContentp);
		//console.log('updatepageitem',newContentp);
					
		if ($(newContent).hasClass('testresult')) {
			var listedTest=$('#'+$(newContent).data('title')+'___'+$(newContent).data('suite')+'___'+$(newContent).data('test')+'___'+$(newContent).data('function'));
			console.log('testres');
			//console.log($(newContent));
			console.log($(listedTest));
			if ($(newContent).hasClass('testresult-passed')) {
				console.log('passed');
				listedTest.removeClass('testresult-failed');
				listedTest.removeClass('testresult-pending');
				listedTest.addClass('testresult-passed');
				$('.showerrorbutton',listedTest).remove();
				$('.detailsbutton',listedTest).remove();
				$('#logfile-'+listedTest.attr('id')).remove();
				updateSuiteStatus(listedTest);
			} else {
				console.log('failed');
				listedTest.removeClass('testresult-passed');
				listedTest.removeClass('testresult-pending');
				listedTest.addClass('testresult-failed');
				updateSuiteStatus(listedTest);
			}
			//$(newContent).appendTo($('#testsuite-'+$(newContent).data('title')));
		} else if ($(newContent).hasClass('testdetails')) {
			console.log('import test details',$(newContent));
			var test=$('#'+$(newContent).data('testid'));
			//console.log($(newContent).data('testid'));
			if ($('.reveal-modal',test).length>0)  {
				//console.log('append');
				var modal=$('.reveal-modal',test);
				modal.append(newContent.html());
			} else {
				//console.log('creates');
				var showButton='';
				console.log($('#logfile-'+$(newContent).data('testid')));
				if ($('#logfile-'+$(newContent).data('testid')).length>0) {
					showButton='<a href="#" class="showerrorbutton button tiny" data-reveal-id="logfile-'+$(newContent).data('testid')+'">Show</a> ';
				}
				
				test.append($('<span>&nbsp;</span><a href="#" class="detailsbutton button tiny" data-reveal-id="testdetails-'+$(newContent).data('testid')+'">Details</a>&nbsp;&nbsp;&nbsp;'+showButton+' <div id="testdetails-'+$(newContent).data('testid')+'" class="testdetailsreveal reveal-modal" data-reveal aria-hidden="true" role="dialog">'+newContent.html()+'<a class="close-reveal-modal" aria-label="Close">&#215;</a></div>'));
			}
			$(document).foundation(); // {'reveal': {'close_on_background_click': true,'close_on_esc': true}});
		} else if ($(newContent).hasClass('phperror')) {
			$('#phperrors').show();
			$('#phperrors').append('<div>'+$(newContent).text()+'</div>');
		
		} else if ($(newContent).hasClass('phperrorlog')) {
			$('#phperrors').show();
			var popup=$('<div><a id="phperrorlogpopupbutton" href="#" class="button tiny" data-reveal-id="phperrorlogpopup" >New PHP Errors</a> <div id="phperrorlogpopup" class="reveal-modal" data-reveal aria-hidden="true" role="dialog">'+newContent.html()+'<a class="close-reveal-modal" aria-label="Close">&#215;</a></div></div>');
			$('#phperrors').prepend(popup);
			$(document).foundation();
			//$('#phperrorlogpopupbutton').click(function() {
			//	$('#phperrors').hide();
			//});
			
		
		} else if ($(newContent).hasClass('logfile')) {
			try {
				$('#logfiles').append($(newContent)); //.find('body').html()
			} catch (e) {}
		} else {
			$('#log').prepend(newContent);
		}
	});
}

function getSuiteStatus(suite) {
	var all=countAllTests(suite);
	var pending=countTests(suite,'pending');
	var failed=countTests(suite,'failed');
	var passed=countTests(suite,'passed');
	//console.log('get suite status',pending,passed,failed,all);
	if (failed>0) {
		return 'failed';
	} else if (passed==all) {
		return 'passed';
	} else {
		return 'pending';
	}
}
function countAllTests(suite) {
	return $('li.testsuite[data-suite="'+suite+'"] div.test .testselected').length;
}
function countSelectedTests(suite) {
	return $('li.testsuite[data-suite="'+suite+'"] div.test .testselected:checked').length;
}
function countTests(suite,status) {
	return $('li.testsuite[data-suite="'+suite+'"] div.test.testresult-'+status+' .testselected').length;
}
function getSelectedTests() {
	var tests=[];
	var allSuites=$('.testsuite').length;
	var selectedSuites=$('.testsuite .suiteselected:checked').length;
	if (allSuites==selectedSuites) {
		// everything selected, run all tests without parameters	
	} else {
		$('.testsuite .suiteselected:checked').each(function(tk,tv) {
			tests.push($(this).parent().data('suite'));
		});
		// now single tests 
		$('.testsuite .suiteselected:not(:checked)').parents('li.testsuite').find('.testselected:checked').each(function() {
			tests.push($(this).parent().attr('id'));
		});
	}
	return tests.join(",");
}



