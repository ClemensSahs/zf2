<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Client;

use Zend\Config\Config;
use Zend\Http\Client\Adapter;

/**
 * This Testsuite includes all Zend_Http_Client that require a working web
 * server to perform. It was designed to be extendable, so that several
 * test suites could be run against several servers, with different client
 * adapters and configurations.
 *
 * Note that $this->baseuri must point to a directory on a web server
 * containing all the files under the files directory. You should symlink
 * or copy these files and set 'baseuri' properly.
 *
 * You can also set the proper constand in your test configuration file to
 * point to the right place.
 *
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class CurlTest extends CommonHttpTests
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = array(
        'adapter'     => 'Zend\Http\Client\Adapter\Curl',
        'curloptions' => array(
            CURLOPT_INFILESIZE => 102400000,
        ),
    );

    protected function setUp()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('cURL is not installed, marking all Http Client Curl Adapter tests skipped.');
        }
        parent::setUp();
    }

    /**
     * @group ZF-3758
     * @link http://framework.zend.com/issues/browse/ZF-3758
     */
    public function testPutFileHandleWithHttpClient()
    {
        $this->client->setUri($this->baseuri . 'testRawPostData.php');
        $putFileContents = file_get_contents(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
            '_files' . DIRECTORY_SEPARATOR . 'staticFile.jpg');

        // Method 2: Using a File-Handle to the file to PUT the data
        $putFilePath = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
            '_files' . DIRECTORY_SEPARATOR . 'staticFile.jpg';
        $putFileHandle = fopen($putFilePath, "r");
        $putFileSize = filesize($putFilePath);

        $adapter = new Adapter\Curl();
        $this->client->setAdapter($adapter);
        $adapter->setOptions(array(
            'curloptions' => array(CURLOPT_INFILE => $putFileHandle, CURLOPT_INFILESIZE => $putFileSize)
        ));
        $this->client->setMethod('PUT');
        $this->client->send();
        $this->assertEquals(gzcompress($putFileContents), gzcompress($this->client->getResponse()->getBody()));
    }

}
