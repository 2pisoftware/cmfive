<?php
// Copyright 2004-present Facebook. All Rights Reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

require_once('__init__.php');

/**
 * An example test case for php-webdriver.
 * 
 * Try running it by 
 *   '../vendor/phpunit/phpunit/phpunit.php ExampleTestCase.php'
 */
class ExampleTestCase extends BasePHPWebDriverTestCase {
  
  public function testTestPageTitle() {
    $this->driver->get($this->getTestPath('index.html'));
    self::assertEquals(
      'php-webdriver test page',
      $this->driver->getTitle()
    );
  }
  
  public function testTestPageWelcome() {
    $this->driver->get($this->getTestPath('index.html'));
    self::assertEquals(
      'Welcome to the facebook/php-webdriver testing page.',
      $this->driver->findElement(WebDriverBy::id('welcome'))->getText()
    );
  }
}