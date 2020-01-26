<?

class LangModel extends Model {

    //private $path = "/var/www/drainware/ddi/lib/lang/";
    private $path = "lib/lang/";
    private $pattern = "/(.*?)\.php/";

    public function __construct() {

        //$this->foo= 5;
    }

    /**
     * Returns an array of languages supported: es, en, de,.. 
     */
    public function getLanguages() {

        $dh = opendir($this->path);
        while (false !== ($filename = readdir($dh))) {
            preg_match($this->pattern, $filename, $matches);
            if (!empty($matches[1]))
                $langs[] = $matches[1];
        }
        closedir($dh);
        // Only Spanish and English support
        $langs = array("en", "es");

        return $langs;
    }

}

?>
