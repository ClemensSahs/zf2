<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Writer;

use XmlWriter as PHPXmlWriter;
use Zend\Config\Writer\Xml as XmlWriter;
use Zend\Config\Config;
use Zend\Config\Reader\Xml as XmlReader;

/**
 * @group      Zend_Config
 */
class XmlTest extends AbstractWriterTestCase
{
    protected $_tempName;

    public function setUp()
    {
        $this->writer = new XmlWriter();
        $this->reader = new XmlReader();
    }

    public function testToString()
    {
        $config = new Config(array(
            'test' => 'foo',
            'bar' => array(
                0 => 'baz',
                1 => 'foo'
            ),
            'nestedArrayString' => array(
                'level1key1' => array(
                    'level2key1' => 'baz',
                    'level2key2' => 'foo'
                ),
                'level1key2' => array(
                    'level2key3' => 'baz',
                    'level2key4' => 'foo'
                ),
            )
        ));

        $configString = $this->writer->toString($config);

        $expected = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
    <nestedArrayString>
        <level1key1>
            <level2key1>baz</level2key1>
            <level2key2>foo</level2key2>
        </level1key1>
        <level1key2>
            <level2key1>baz</level2key1>
            <level2key2>foo</level2key2>
        </level1key2>
    </nestedArrayString>
</zend-config>

ECS;

        $this->assertEquals($expected, $configString);
    }

    public function testSectionsToString()
    {
        $config = new Config(array(), true);
        $config->production = array();

        $config->production->webhost = 'www.example.com';
        $config->production->database = array();
        $config->production->database->params = array();
        $config->production->database->params->host = 'localhost';
        $config->production->database->params->username = 'production';
        $config->production->database->params->password = 'secret';
        $config->production->database->params->dbname = 'dbproduction';

        $configString = $this->writer->toString($config);

        $expected = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <production>
        <webhost>www.example.com</webhost>
        <database>
            <params>
                <host>localhost</host>
                <username>production</username>
                <password>secret</password>
                <dbname>dbproduction</dbname>
            </params>
        </database>
    </production>
</zend-config>

ECS;

        $this->assertEquals($expected, $configString);
    }
}
