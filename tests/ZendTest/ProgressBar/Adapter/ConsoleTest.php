<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ProgressBar\Adapter;

use Zend\ProgressBar\Adapter;

require_once 'MockupStream.php';

/**
 * @group      Zend_ProgressBar
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        stream_wrapper_register("zendprogressbaradapterconsole", 'ZendTest\ProgressBar\Adapter\MockupStream');
    }

    protected function tearDown()
    {
        stream_wrapper_unregister('zendprogressbaradapterconsole');
    }

    public function testWindowsWidth()
    {
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $adapter = new Stub();
            $adapter->notify(0, 100, 0, 0, null, null);
            $this->assertEquals(79, strlen($adapter->getLastOutput()));
        } else {
            $this->markTestSkipped('Not testable on non-windows systems');
        }
    }

    public function testStandardOutputStream()
    {
        $adapter = new Stub();

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stdout', $metaData['uri']);
    }

}

class Stub extends Adapter\Console
{
    protected $_lastOutput = null;

    public function getLastOutput()
    {
        return $this->_lastOutput;
    }

    protected function _outputData($data)
    {
        $this->_lastOutput = $data;
    }
}
