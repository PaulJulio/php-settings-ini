<?php
use \PaulJulio\SettingsIni\Settings;
use \PaulJulio\SettingsIni\SettingsSO;
use \PaulJulio\SettingsIni\SettingsTestExtension;

class SettingsTest extends PHPUnit_Framework_TestCase {

    /* @var SettingsSO */
    private $somock;

    protected function setUp() {
        parent::setUp();
        $this->somock = Mockery::mock(SettingsSO::class);
        $this->somock->shouldReceive('isValid')
            ->once()
            ->andReturn(true);
        $this->somock->shouldReceive('getSettingsFileNames')
            ->once()
            ->andReturn([realpath(__DIR__ . DIRECTORY_SEPARATOR . '01_test.ini'),
            realpath(__DIR__ . DIRECTORY_SEPARATOR . '02_test.ini')]);
    }

    protected function tearDown() {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * ensure the mock is set up properly before trying to use it
     */
    public function testMock() {
        $this->assertTrue($this->somock->isValid());
        $this->assertEquals([realpath(__DIR__ . DIRECTORY_SEPARATOR . '01_test.ini'),
            realpath(__DIR__ . DIRECTORY_SEPARATOR . '02_test.ini')],
            $this->somock->getSettingsFileNames());
    }

    /**
     * @expectedException Exception
     */
    public function testFactory() {
        // the Factory ensures the SettingsSO is valid and then calls some private methods, tested below
        $s = Settings::Factory($this->somock);
        $this->assertTrue($s instanceof Settings);
        $s = Settings::Factory(new SettingsSO());
    }

    public function testSetIniFileNames() {
        $sr = new \ReflectionClass(Settings::class);
        $instance = $sr->newInstanceWithoutConstructor();
        $m1 = $sr->getMethod('setIniFileNames');
        $m1->setAccessible(true);
        $m1->invoke($instance, $this->somock->getSettingsFileNames());
        $p1 = $sr->getProperty('iniFileNames');
        $p1->setAccessible(true);
        $this->assertEquals([realpath(__DIR__ . DIRECTORY_SEPARATOR . '01_test.ini'),
            realpath(__DIR__ . DIRECTORY_SEPARATOR . '02_test.ini')],
            $p1->getValue($instance));
        $this->somock->isValid(); // easier than fixing the mock declaration
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     *
     * because $instance->value11 is not set
     */
    public function testLoadFiles() {
        $sr = new \ReflectionClass(Settings::class);
        $instance = $sr->newInstanceWithoutConstructor();
        $p1 = $sr->getProperty('iniFileNames');
        $p1->setAccessible(true);
        $p1->setValue($instance, $this->somock->getSettingsFileNames());
        $m2 = $sr->getMethod('loadFiles');
        $m2->setAccessible(true);
        $m2->invoke($instance);
        $this->somock->isValid(); // easier than fixing the mock declaration
        $this->assertEquals(1, $instance->value01);
        $this->assertEquals(2, $instance->value02);
        $this->assertEquals(3, $instance->value03);
        $this->assertSame(['a'=>'a','b'=>'b','c'=>'c'], $instance->value04);
        $this->assertEquals([0,1,2], $instance->value24);
        $this->assertNull($instance->value11);
    }
}
