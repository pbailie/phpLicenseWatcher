<?php
namespace PhpLicenseWatcher\Util;

/** 
 * Class used to reference values in config.ini file.
 * 
 * @author pbailie@users.noreply.github.com
 */
class config {
    private \stdClass $conf;

    public function __construct() {
        $this->conf = new \stdClass;

        // I am evaluating and including the root_dir in class config so that file paths can be better written.
        preg_match("/^(\S*)[\/\\]lib/", __DIR__, $matches);
        $this->conf->root_dir = $matches[1];
        
        // Like so...
        $root_dir = $this->conf->root_dir;  // Elsewhere, do...  $root_dir = $config->root_dir;
        $ini = parse_ini_file("{$root_dir}/config/config.ini", true, INI_SCANNER_RAW);

        // And fill in the config from ini file.
        foreach($ini as $section => $properties) {
            $this->conf->$section = new \stdClass;
            foreach ($properties as $name => $value)
                $this->conf->$section->$name = $value;
        }
    }

    /** 
     * This is overriding a PHP magic method.
     *
     * @example $db_host = $config->database->host
     * @see https://www.php.net/manual/en/language.oop5.magic.php
     * @see https://www.php.net/manual/en/language.oop5.overloading.php#object.get
     */
    public function __get($name) {
        if (property_exists($this->conf, $name))
            return $this->conf->$name;
        else
            throw new \Exception("\"{$name}\" is not defined in config.ini.");
    }
}

// EOF
