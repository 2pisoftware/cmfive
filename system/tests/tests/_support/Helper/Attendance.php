<?php 
namespace Helper;
class Attendance extends \Codeception\Module  {
	/*********************
	 * List of users in authority for testing
	 *********************/
	static  $users=[
		'payroll' => [
			'username'=>'2piPayrollOfficer',
			'password'=>'password',
			'employee_number' => '95074'
		],
		'user' => [
			'username'=>'2piUser',
			'password'=>'password',
			'employee_number' => '95075'
		],
		'trickyuser' => [
			'username'=>"2pi O'-",
			'password'=>'password',
			'employee_number' => '95077'
		],		
		'supervisor' => [
			'username'=>'2piSupervisor',
			'password'=>'password',
			'employee_number' => '95076'
		],
		'wayne' => [
			'username'=>'',
			'password'=>'',
			'employee_number' => '11010'
		]
	];
	
	/************************
	 * Login as a preconfigured user
	 ************************/
	public function loginAs($I,$user,$capture=true) {
		$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
	}
	
	/************************
	 * Create a new timesheet
	 * @return int $id the timesheet id
	 ************************/
	public function createTimeSheet($I,$user,$date,$capture=true) {
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		$I->amOnPage('/attendance');
		$I->selectOption('select.fortnightending',$date);
		$I->wait(5);
		if ($capture) $I->makeScreenshot('Created a timesheet for '.$user.' in fortnight ending' .$date);
		//$id=$I->executeJS("return $('.timesheet_list).attr('data-id');");
		$id=$I->grabAttributeFrom('.timesheet_list','data-id');
		return $id;
	}
	/************************
	 * Submit a timesheet 
	 ************************/
	public function submitTimeSheet($I,$user,$date,$capture=true) {
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		$I->amOnPage('/attendance');
		$I->selectOption('select.fortnightending',$date);
		$I->wait(5);
		if ($capture) $I->makeScreenshot('Login as '.$user.', Select a pay period and click Submit to send timesheet for approval');
		
		$I->click('Submit');
		// bypass vha banking
		$I->wait(5);
		if ($capture) $I->makeScreenshot('Choose to bank time if available');
		$I->click('#cmfive-modal input[name=submit]');
	}
	/************************
	 * Submit a timesheet 
	 ************************/
	public function resetTimeSheet($I,$user,$date,$capture=true) {
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		$I->amOnPage('/attendance');
		$I->selectOption('select.fortnightending',$date);
		$I->wait(5);
		if ($capture) $I->makeScreenshot('Click reset to remove all modifications to timesheet');
		$I->click('Reset');
		//$I->acceptPopup();
	}
	/************************
	 * Approve a timesheet 
	 ************************/
	public function approveTimeSheet($I,$approveUser,$date,$capture=true) {
		//$user='supervisor';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/descendants');
		$I->click('My Employees');
		if ($capture) $I->makeScreenshot('Login as a supervisor and navigate to My Employees page and choose a pay period');
		$I->selectOption('select.fortnightending',$date);
		$I->wait(5);
		// choose the user
		if (self::$users[$approveUser]['employee_number']>0) {
			$I->click('.tablesorter tbody tr[data-id="'.self::$users[$approveUser]['employee_number'].'"] a');
			if ($capture) $I->makeScreenshot('Click Approve to accept the timesheet');
			$I->click('Approve');
			$I->see('Overtime Claimed');
			$I->selectOption('#timesheet_approval_form input[name=accept]','Yes');
			if ($capture) $I->makeScreenshot('Choose Yes to acknowledge the overtime claim and click Submit');
			$I->click('Submit');
		}
	}
	/************************
	 * Reject a timesheet 
	 ************************/
	public function rejectTimeSheet($I,$approveUser,$date,$capture=true) {
		//$user='supervisor';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/descendants');
		$I->click('My Employees');
		$I->selectOption('select.fortnightending',$date);
		$I->wait(5);
		if ($capture) $I->makeScreenshot('Login as a supervisor, navigate to My Employees then choose a pay period');
		// choose the user
		if (self::$users[$approveUser]['employee_number']>0) {
			$I->click('.tablesorter tbody tr[data-id="'.self::$users[$approveUser]['employee_number'].'"] a');
			// Approve to reject in popup dialog
			if ($capture) $I->makeScreenshot('Click Reject to send the timesheet back to the user');
			$I->fillField('#supervisor_comment','Test Rejection');
			$I->click('Reject');
			//$I->see('Overtime Claimed');
			//$I->selectOption('#timesheet_approval_form input[name=accept]','No');
			//$I->click('Submit');
		}
	}
	/************************
	 * Rollback a timesheet 
	 ************************/
	public function rollbackTimeSheet($I,$approveUser,$date,$capture=true) {
		//$user='payroll';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/list');
		$I->click('Employee Admin');
		if ($capture) $I->makeScreenshot('Login as a payroll officer, navigate to Employee Admin and click the Timesheet button for a user');
		if (self::$users[$approveUser]['employee_number']>0) {
			$I->click('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$approveUser]['employee_number'].'"] a.timesheetlink');
			$I->selectOption('select.fortnightending',$date);
			$I->wait(5);
			if ($capture) $I->makeScreenshot('Click Rollback to revert the timesheet');
			$I->click('Rollback');
			//$I->acceptPopup();
		}
	}
	/************************
	 * Submit a timesheet 
	 ************************/
	public function deleteTimeSheet($I,$approveUser,$date,$capture=true) {
		//$user='payroll';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/list');
		$I->click('Employee Admin');
		if ($capture) $I->makeScreenshot('Login as a payroll officer, navigate to Employee Admin and click the Timesheet button for a user');
		if (self::$users[$approveUser]['employee_number']>0) {
			$I->click('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$approveUser]['employee_number'].'"] a.timesheetlink');
			$I->selectOption('select.fortnightending',$date);
			$I->wait(5);
			if ($capture) $I->makeScreenshot('Click Delete to remove the timesheet and allow the user to start fresh');
		
			$I->click('Delete');
			//$I->acceptPopup();
		}
	}
	
	/************************
	 * Check that a time sheet status matches what you expect
	 ************************/
	public function checkTimeSheetStatus($I,$checkUser,$date,$status,$capture=true) {
		//$user='payroll';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/list');
		$I->click('Employee Admin');
		if (self::$users[$checkUser]['employee_number']>0) {
			$I->click('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"] a.timesheetlink');
			$I->selectOption('select.fortnightending',$date);
			$I->wait(5);
			if ($capture) $I->makeScreenshot('Check timesheet status matches '.$status);
			$I->see('Timesheet '.$status);
		}
	}
	
	/************************
	 * Run the user import script
	 ************************/
	public function importUsers($I,$capture=true) {
		//$user='payroll';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		$I->amOnPage('/attendance/fetch_employee');
		if ($capture) $I->makeScreenshot('Import Users');
	}
	
	/************************
	 * Enable employee features
	 * Set Type=Variable,RDO Type=Monthly,Start Time=9:00AM,Lunch=45,Variable Hours Allowed=true
	 ************************/
	public function enableEmployeeFeatures($I,$checkUser,$capture=true) {
		//$user='payroll';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/list');
		if ($capture) $I->makeScreenshot('Login in as payroll officer, navigate to Employee Admin and update values for '.$checkUser.' '); // (Type=Variable,RDO Type=Monthly,Start Time=9:00AM,Lunch=45,Variable Hours Allowed=true and Standard Working Days for 5 every day plus balance on last day)
		$I->click('Employee Admin');
		if (self::$users[$checkUser]['employee_number']>0) {
			// set employee type to Variable
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="type"]','Variable');
			// set rdo type to Monthly
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="rdo_type"]','Monthly RDO');
			// set start time to 9am
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="default_start_time"]','9:00AM');
			// set lunch to 45 minutes
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="lunch_length"]','45');
			// enable variable hours
			$I->checkOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  input[data-field="vwh_allowed"]');
			
			//set start date
			$I->executeJS("
				$('#employeeList tr.editablelistrow[data-employeenumber=\"".self::$users[$checkUser]['employee_number']."\"] button[data-field=\"vwh_start_date\"]').get(0).click();
				// wednesday first week of month
				$('.picker__table tbody tr:eq(0) td:eq(3) .picker__day').get(0).click();
			");
			// selected date
			$date=$I->executeJS("return $('#employeeList tr.editablelistrow[data-employeenumber=\"".self::$users[$checkUser]['employee_number']."\"] button[data-field=\"vwh_start_date\"]').text();");
			$I->assertNotEquals($date,'Set');
			//$I->click('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"] button[data-field="vwh_start_date"]');
			//$I->wait(1);
			//$I->click('.picker tbody tr:nth-child(1) td:nth-child(2) .picker__day');
			
			// set standard work hours
			$I->click('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"] a.dayeditbutton');
			$I->executeJS("
			$('#cmfive-modal input[type=\"text\"]').val(5);
				var a=$('#cmfive-modal .tally');
				var b=$.trim(a.text()).split(' ');
				var c=b[2];
				var d=parseInt(c) - 65;  // sum of 5 and 4s above
				$('#cmfive-modal input[type=\"text\"]').last().val(d);
				$('#cmfive-modal input[type=\"text\"]').last().change();
				$('#cmfive-modal .savebutton').click();
			 ");
						
			$I->wait(5);
			// reload page and check that values persisted
			$I->amOnPage('/attendance-employee/list');
			if ($capture) $I->makeScreenshot('Reload page and check values saved to database');
			$I->assertEquals('Variable',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="type"]'));
			$I->assertEquals('Monthly RDO',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="rdo_type"]'));
			$I->assertEquals('9:00AM',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="default_start_time"]'));
			$I->assertEquals('45',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="lunch_length"]'));
			$I->assertEquals('on',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  input[data-field="vwh_allowed"]'));
			$date=$I->executeJS("return $('#employeeList tr.editablelistrow[data-employeenumber=\"".self::$users[$checkUser]['employee_number']."\"] button[data-field=\"vwh_start_date\"]').text();");
			$I->assertNotEquals($date,'Set');
			
			
			//$I->makeScreenshot('Check timesheet status matches '.$status);
			//$I->see('Timesheet '.$status);
		}
	}
	
	/************************
	 * Disable employee features
	 * Disable RDO and variable hours, set type to fixed,set lunch to 15 minutes, set start time to 5am
	 ************************/
	public function disableEmployeeFeatures($I,$checkUser,$capture=true) {
		//$user='payroll';
		//$I->login($I,self::$users[$user]['username'],self::$users[$user]['password']);
		//$I->amOnPage('/attendance-employee/list');
		if ($capture) $I->makeScreenshot('Login in as payroll officer, navigate to Employee Admin and update values for '.$checkUser.' '); 
		$I->click('Employee Admin');
		if (self::$users[$checkUser]['employee_number']>0) {
			// set employee type to Variable
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="type"]','Fixed');
			// set rdo type to Monthly
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="rdo_type"]','Casual/Part time');
			// set start time to 9am
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="default_start_time"]','5:00AM');
			// set lunch to 45 minutes
			$I->selectOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="lunch_length"]','15');
			// enable variable hours
			$I->uncheckOption('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  input[data-field="vwh_allowed"]');
			$I->wait(5);
			
			// reload page and check that values persisted
			$I->amOnPage('/attendance-employee/list');
			if ($capture) $I->makeScreenshot('Reload page and check values saved to database');
			$I->assertEquals('Fixed',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="type"]'));
			$I->assertEquals('Casual/Part time',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="rdo_type"]'));
			$I->assertEquals('5:00AM',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="default_start_time"]'));
			$I->assertEquals('15',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  select[data-field="lunch_length"]'));
			//$I->assertEquals('off',$I->grabValueFrom('#employeeList tr.editablelistrow[data-employeenumber="'.self::$users[$checkUser]['employee_number'].'"]  input[data-field="vwh_allowed"]'));
		}
	}
	
	
	
	/************************
	 * Submit a timesheet 
	 ************************/
	public function checkDayViewNavigation($I) {
		
	} 
	
	public function setEntryStartEndTimes($I,$entryPos,$startHour,$startMinute,$startAmPm,$endHour,$endMinute,$endAmPm) {
		// start
		$I->click('.timesheet_list_item::nth-child('.$entryPos.') .active_time_entry .starttime');
		$I->selectOption('#timepicker_modal_hours',$startHour);
		$I->selectOption('#timepicker_modal_minutes',$startMinute);
		$I->checkOption('#timepicker_modal_'.$startAmPm);
		$I->click('Save');
		// end
		$I->click('.timesheet_list_item::nth-child('.$entryPos.') .active_time_entry .endtime');
		$I->selectOption('#timepicker_modal_hours',$endHour);
		$I->selectOption('#timepicker_modal_minutes',$endMinute);
		$I->checkOption('#timepicker_modal_'.$endAmPm);
		$I->click('Save');
	}
	
	/************************
	 * Submit a timesheet 
	 ************************/
	public function updateTimeSheet($I,$capture=true) {
		$I->dontSee('Access Denied');
		$I->see('2piUser - Timesheet');
		// fortnight selector
		$I->selectOption('select.fortnightending','14-08-2015');		
		// see authority leave in list and day view
		
		$I->click('.timesheet_list_item::nth-child(1)');
		$I->see('Saturday 1st of August');
		// next previous buttons
		$I->click('.tomorrowbutton');
		$I->see('Sunday 2nd of August');
		$I->click('.yesterdaybutton');
		$I->see('Saturday 1st of August');
		
		// check default lunch length, start end times
		$lunch=$I->grabValueFrom('.lunchlength');
		//echo $lunch;
		$timesheetId=$I->executeJS("return $('#timesheet_list').data('id');");
		
		// set lunch
		
		// set rdo
		
		// set vha
		
		// set start end times
		
		
		// back button
		
		// marked day approved
		
		// check stats against preconfigured data in sql dump
		
		// check time banks increment after submitting and approving a timesheet
		
		// reset
		
		// add rows
		
		// delete rows
		
		// change leave type (and see textarea for comment)
		
		// submit with comment
		
		
		// disabled for editing ?
		
	}
	
 
}
