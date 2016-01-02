<?php
namespace PaulJulio\SettingsIni;

class Settings {

    /* @var array */
    private $iniFileNames;

    private function __construct() {}

    /**
     * @param SettingsSO $so
     * @return Settings
     * @throws \Exception
     */
    public static function Factory(SettingsSO $so) {
        if (!$so->isValid()) {
            throw new \Exception('Invalid Settings Object');
        }
        $instance = new static;
        $instance->setIniFileNames($so->getSettingsFileNames());
        $instance->loadFiles();
        return $instance;
    }

    private function setIniFileNames(array $fns) {
        $this->iniFileNames = $fns;
    }

    private function loadFiles() {
        $environments  = array();
        $envToLoad     = null;
        $unique        = array();
        foreach ($this->iniFileNames as $fn) {
            $iniValues = parse_ini_file($fn, true);
            // ini sections correspond to environments
            // the ini file may use dot notation to indicate array depth
            foreach ($iniValues as $environment => $eVals) {
                if (!is_array($eVals)) {
                    continue;
                }
                if (!isset($environments[$environment])) {
                    $environments[$environment] = array();
                }
                foreach ($eVals as $key => $value) {
                    $keyParts     = explode('.', $key);
                    $currentValue = &$environments[$environment];
                    $lastKey      = array_pop($keyParts);
                    if ($lastKey == '') {
                        $lastKey = 0;
                    }
                    foreach ($keyParts as $k) {
                        if ($k == '') {
                            $k = 0;
                        }
                        if (!isset($currentValue[$k])) {
                            $currentValue[$k] = array();
                        }
                        $currentValue = &$currentValue[$k];
                    }
                    $currentValue[$lastKey] = $value;
                }
            }
            // only accept environment declarations from the first ini file loaded
            if (!isset($envToLoad)) {
                if (isset($environments['unique'])) {
                    $unique = $environments['unique'];
                }
                $envToLoad = explode(',', $unique['environments']);
                // check for auto and substitute
                if (!in_array('unique', $envToLoad)) {
                    array_unshift($envToLoad, 'unique');
                }
            }
        }
        // replace the unique environment with the first unique section encountered
        $environments['unique'] = $unique;
        foreach(array_unique($envToLoad) as $environment) {
            if (array_key_exists($environment, $environments)) {
                if (!is_array($environments[$environment])) {
                    continue;
                }
                foreach ($environments[$environment] as $property => $value) {
                    if (isset($this->$property) && is_array($this->$property) && is_array($value)) {
                        $this->$property = $this->array_merge_recursive_distinct($this->$property, $value);
                    } else {
                        $this->$property = $value;
                    }
                }
            }
        }
    }

    private function array_merge_recursive_distinct(array $array1, $array2 = null) {
        $merged = $array1;
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $merged[$key] = is_array($merged[$key]) ? $this->array_merge_recursive_distinct($merged[$key],
                        $array2[$key]) : $array2[$key];
                } else {
                    $merged[$key] = $val;
                }
            }
        }
        return $merged;
    }
}