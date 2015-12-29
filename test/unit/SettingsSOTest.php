<?php

class SettingsSOTest extends PHPUnit_Framework_TestCase {

    public function testFileNameProperty() {
        $this->assertClassHasAttribute('settingsFileNames', \PaulJulio\SettingsIni\SettingsSO::class);
        $so = new \PaulJulio\SettingsIni\SettingsSO();
        $this->assertInternalType('array', PHPUnit_Framework_Assert::readAttribute($so, 'settingsFileNames'));
    }

    public function testAddFileNames() {
        $so = new \PaulJulio\SettingsIni\SettingsSO();
        $this->assertArrayNotHasKey(0, PHPUnit_Framework_Assert::readAttribute($so, 'settingsFileNames'));
        $so->addSettingsFileName('test value');
        $this->assertArrayHasKey(0, PHPUnit_Framework_Assert::readAttribute($so, 'settingsFileNames'));
        $this->assertSame(['test value'], PHPUnit_Framework_Assert::readAttribute($so, 'settingsFileNames'));
        $this->assertArrayNotHasKey(1, PHPUnit_Framework_Assert::readAttribute($so, 'settingsFileNames'));
        $so->addSettingsFileName('test value 2');
        $this->assertSame(['test value', 'test value 2'], PHPUnit_Framework_Assert::readAttribute($so, 'settingsFileNames'));
    }

    public function testGetFileNames() {
        $so = new \PaulJulio\SettingsIni\SettingsSO();
        $this->assertSame([], $so->getSettingsFileNames());
        $sor = new ReflectionClass($so);
        $sorp = $sor->getProperty('settingsFileNames');
        $sorp->setAccessible(true);
        $sorp->setValue($so, ['test value']);
        $this->assertSame(['test value'], $so->getSettingsFileNames());
    }

    public function testIsValid() {
        $so = new \PaulJulio\SettingsIni\SettingsSO();
        $this->assertFalse($so->isValid());
        $sor = new ReflectionClass($so);
        $sorp = $sor->getProperty('settingsFileNames');
        $sorp->setAccessible(true);
        $sorp->setValue($so, [__FILE__]);
        $this->assertTrue($so->isValid());
        $sorp->setValue($so, [__FILE__, __DIR__ . DIRECTORY_SEPARATOR . 'fileNotFound.txt']);
        $this->assertFalse($so->isValid());
    }

    public function testResetFileNames() {
        $so = new \PaulJulio\SettingsIni\SettingsSO();
        $sor = new ReflectionClass($so);
        $sorp = $sor->getProperty('settingsFileNames');
        $sorp->setAccessible(true);
        $sorp->setValue($so, [__FILE__]);
        $so->resetFileNames();
        $this->assertSame([], $sorp->getValue($so));
    }

    public function testAddIniFileNamesFromPath() {
        $so = new \PaulJulio\SettingsIni\SettingsSO();
        $so->addIniFileNamesFromPath(__DIR__);
        $sor = new ReflectionClass($so);
        $sorp = $sor->getProperty('settingsFileNames');
        $sorp->setAccessible(true);
        $this->assertSame(
            [__DIR__ . DIRECTORY_SEPARATOR . '01_test.ini',
            __DIR__ . DIRECTORY_SEPARATOR . '02_test.ini'],
            $sorp->getValue($so)
        );
    }
}