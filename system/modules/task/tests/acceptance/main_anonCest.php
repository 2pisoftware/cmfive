<?php
use \AcceptanceTester;
/**
 * @guy AcceptanceTester\CM5WebGuySteps
 */
class main_anonCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
	var $username='admin';
	var $password='admin';
    var $navSelector='Home';
     
    public function runTests(AcceptanceTester\CM5WebGuySteps $I) {
		$I->amOnPage('/auth/login');
		$I->fillField('login',$this->username);
		$I->fillField('password',$this->password);
		$I->click('Login');
		//$I->login($this->username,$this->password);
		$I->amOnPage("/");
		$I->see("Home");
		//$I->executeJS("$('#topnav_example a').get(0).click();");
		
		$I->click('Report');
		$I->click('Report');
		/*$I->executeInSelenium(function(\WebDriver $webDriver) {
			$element=$webDriver->findElement(WebDriverBy::cssSelector("#topnav_example a"));
			$element->click();
		});*/
		//$I->click('a','#topnav_example');
		//$I->click(['link','Example']);
		//$I->amOnPage('/example');
		//$I->seeInCurrentUrl('/example');
		//$I->see('Create a new task');
		$I->see('Search Reports');
		/*
		$I->click('button.button');
		$I->seeCheckOption('//*[@id="cmfive-modal"]/form/div[1]/div[4]/div/label/input');
		
		$I->seeElementInDom('//*[@id="cmfive-modal"]/form/div[1]/div[4]/div/label/input');
		
		$myVar = $I->executeJS("return $('#input[type=\"checkbox\"]').val()");
		$I->wantTo('sdfsdf'.$myVar);
		* */
	}
	

	
}
