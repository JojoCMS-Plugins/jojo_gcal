<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_AppTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fileName = 'Zend/Gdata/App/_files/FeedSample1.xml';
    }

    public function testImportFile()
    {
        $feed = Zend_Gdata_App::importFile($this->fileName, 
                'Zend_Gdata_App_Feed', true);
        $this->assertEquals('dive into mark', $feed->title->text);
    }

    public function testSetAndGetHttpMethodOverride()
    {
        Zend_Gdata_App::setHttpMethodOverride(true);
        $this->assertEquals(true, Zend_Gdata_App::getHttpMethodOverride());
    }
}
