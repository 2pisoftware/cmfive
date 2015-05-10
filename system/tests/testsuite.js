
var testsRunning=false;
// prevent close window losing test results
window.onbeforeunload = function() {
    return 'You will lose your test results!';
}

function initialisePage() {
	$(document).foundation(); // {'reveal': {'close_on_background_click': true,'close_on_esc': true}}); {'reveal': {'close_on_background_click': true,'close_on_esc': true}});
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
	var xhr ;
	
	
	function startTests(tests) {
		UIStartTests();
		xhr= new XMLHttpRequest();
		var lastContent='';
		var currentContent='';
		var locationParts=document.location.href.split("/");
		var testLocation=locationParts.slice(0,locationParts.length-1).join("/")+'/'+tests;
		xhr.open("GET", testLocation, true);
		xhr.onprogress = function(e) {
			var lastContent=currentContent;
			currentContent=e.currentTarget.responseText;
			newContent=currentContent.substr(lastContent.length);
			updatePage(newContent);
		}
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4) {
				UIStopTests();
			}
		}
		xhr.send();
	}
	$('.runtestbutton').click(function() {
		if (testsRunning==false)  {
			startTests($(this).attr('href')); //$(this).parent().attr('id'));
		}
		return false;
	});
	$('.runtestsuitebutton').click(function() {
		if (testsRunning==false)  {
			startTests($(this).attr('href')); //$(this).parents('li').first().data('suite'));
		}
		return false;
	});
    $('#runbutton').click(function() {
		if (testsRunning==false)  {
			startTests('runsuite.php?tests='+getSelectedTests());
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
}
function UIStopTests() {
	testsRunning=false;
	$('#runbutton').show();
	$('#stopbutton').hide();
	
}


function updateSuiteStatus(listedTest) {
	//check if all passed and update parent
	var suiteDOM=listedTest.parents('li.testsuite').first();
	var contentDOM=listedTest.parents('li.testsuite').find('div.content').first();
	var suite=suiteDOM.data('suite');
	var status=getSuiteStatus(suite);
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
	var status='pending';
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
	//console.log('updatepage',latestContent);
	var newContentWrapped=$('<div>'+latestContent+'</div>');
	//console.log('wrapped',newContentWrapped.html());
	$.each(newContentWrapped.children(),function(nk,newContentp) {
		var newContent=$(newContentp);
		//console.log('updatepageitem',newContentp);
		$('#log').prepend(newContent);
					
		if ($(newContent).hasClass('testresult')) {
			var listedTest=$('#'+$(newContent).data('title')+'___'+$(newContent).data('suite')+'___'+$(newContent).data('test')+'___'+$(newContent).data('function'));
			console.log('testres');
			console.log($(newContent));
			if ($(newContent).hasClass('testresult-passed')) {
				listedTest.removeClass('testresult-failed');
				listedTest.removeClass('testresult-pending');
				listedTest.addClass('testresult-passed');
				updateSuiteStatus(listedTest);
			} else {
				listedTest.removeClass('testresult-passed');
				listedTest.removeClass('testresult-pending');
				listedTest.addClass('testresult-failed');
				updateSuiteStatus(listedTest);
			}
			//$(newContent).appendTo($('#testsuite-'+$(newContent).data('title')));
		} else if ($(newContent).hasClass('testdetails')) {
			//console.log('import test details',$(newContent));
			var test=$('#'+$(newContent).data('testid'));
			//console.log($(newContent).data('testid'),test.text(),test);
			if ($('.reveal-modal',test).length>0)  {
				var modal=$('.reveal-modal',test);
				modal.append(newContent.html());
			} else {
				test.append($('<span>&nbsp;</span><a href="#" class="button tiny" data-reveal-id="testdetails-'+$(newContent).data('testid')+'">Details</a> <a href="#" class="showbutton button tiny" data-reveal-id="logfile-'+$(newContent).data('testid')+'">Show</a> <div id="testdetails-'+$(newContent).data('testid')+'" class="reveal-modal" data-reveal aria-hidden="true" role="dialog">'+newContent.html()+'<a class="close-reveal-modal" aria-label="Close">&#215;</a></div>'));
			}
			$(document).foundation(); // {'reveal': {'close_on_background_click': true,'close_on_esc': true}});
		} else if ($(newContent).hasClass('logfile')) {
			$('#logfiles').append($(newContent).html());
		}
	});
}

function getSuiteStatus(suite) {
	var all=countAllTests(suite);
	var pending=countTests(suite,'pending');
	var failed=countTests(suite,'failed');
	var passed=countTests(suite,'passed');
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
	return $('li.testsuite[data-suite="'+suite+'"] div.testresult-'+status+' .testselected').length;
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



