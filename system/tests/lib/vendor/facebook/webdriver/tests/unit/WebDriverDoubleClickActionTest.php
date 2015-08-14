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

namespace Facebook\WebDriver;

use Facebook\WebDriver\Interactions\Internal\WebDriverDoubleClickAction;

class WebDriverDoubleClickActionTest extends \PHPUnit_Framework_TestCase {
  /**
   * @type WebDriverDoubleClickAction
   */
  private $webDriverDoubleClickAction;

  private $webDriverMouse;
  private $locationProvider;

  public function setUp() {
    $this->webDriverMouse = $this->getMock('Facebook\WebDriver\WebDriverMouse');
    $this->locationProvider = $this->getMock('Facebook\WebDriver\Internal\WebDriverLocatable');
    $this->webDriverDoubleClickAction = new WebDriverDoubleClickAction(
      $this->webDriverMouse,
      $this->locationProvider
    );
  }

  public function testPerformSendsDoubleClickCommand() {
    $coords = $this->getMockBuilder('Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates')
      ->disableOriginalConstructor()->getMock();
    $this->webDriverMouse->expects($this->once())->method('doubleClick')->with($coords);
    $this->locationProvider->expects($this->once())->method('getCoordinates')->will($this->returnValue($coords));
    $this->webDriverDoubleClickAction->perform();
  }
}
