<?php
namespace PaulJulio\SettingsIni;

/**
 * Class SettingsSO
 * @package PaulJulio\SettingsIni
 *
 * This is the settings object for the Settings class, probably the most confusing naming possible for this scheme.
 * This follows the Empty Constructor Pattern that I am trying to enforce in all my code. A constructor may take
 * 0 arguments (like this class) with a public constructor, otherwise it should have a private constructor and a
 * factory method that takes one argument (like the associated Settings class) which must be a settings object
 * (thus the SO suffix).
 */
class SettingsSO {

    private $settingsFileNames = [];

    /**
     * @return bool
     */
    public function isValid() {
        if (!isset($this->settingsFileNames)) {
            return false;
        }
        if (count($this->settingsFileNames) == 0) {
            return false;
        }
        foreach ($this->settingsFileNames as $fn) {
            if (!file_exists(realpath($fn))) {
                return false;
            }
        }
        return true;
    }
    /**
     * @return string[]
     */
    public function getSettingsFileNames() {
        return $this->settingsFileNames;
    }

    /**
     * @param string $settingsFileName
     */
    public function addSettingsFileName($settingsFileName) {
        $this->settingsFileNames[] = $settingsFileName;
    }

    public function addIniFileNamesFromPath($path) {
        $glob = glob(realpath($path) . DIRECTORY_SEPARATOR . '*.ini');
        foreach ($glob as $fn) {
            $this->addSettingsFileName($fn);
        }
    }

    public function resetFileNames() {
        $this->settingsFileNames = [];
    }

}