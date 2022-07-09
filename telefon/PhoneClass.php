<?php

class PhoneClass
{

    public $wpdb;
    public $phoneTable;

    public function __construct()
    {
        global  $wpdb;
        $this->wpdb = $wpdb;
        $this->phoneTable = $this->wpdb->prefix.'phone';
        /*
        register_activation_hook( __FILE__, array($this,"activatePhonePlugin") );
        register_deactivation_hook( __FILE__, array($this,"deactivatePhonePlugin") );

        add_shortcode('stary_telefon', array($this,'fn_stary_telefon'));
        add_action( 'rest_api_init', array($this,'initRestApi') );
        add_action( 'wp_enqueue_scripts', array($this,'enqueueScript') );
        add_action( 'wp_enqueue_scripts', array($this,'enqueueStyle') );
        */

        //$file = $_SERVER['DOCUMENT_ROOT'].'/test-1.txt';
        //$tmp = print_r(__FILE__, true);
        //$tmp .= "\n-4-4-4-4-\n";
        //$tmp .= print_r(date('Y-m-d H:i s'), true);
        //$tmp .= "\n===============\n";
        //$tmp .= print_r($this, true);
        //file_put_contents($file,$tmp);

    }

    public function initRestApi() {
        register_rest_route( 'wl/v1', 'posts/(?P<input>[a-zA-Z\-]+)', array(
            'methods' => 'GET',
            'callback' => array($this,'callbackResponse'),
        ) );
    }

    public function activatePhonePlugin()
    {
        $this->initDbPhonePlugin();

    }

    public function deactivatePhonePlugin()
    {
        $this->deactivateDbPhonePlugin();
        $this->removePhoneshortcode();
    }

    public function initDbPhonePlugin()
    {

        $sql = "CREATE TABLE IF NOT EXISTS `$this->phoneTable` (";
        $sql .= " `id` int(11) NOT NULL auto_increment, ";
        $sql .= " `input` varchar(100) NOT NULL, ";
        $sql .= " `output` varchar(100) NOT NULL, ";
        $sql .= " `data` datetime DEFAULT CURRENT_TIMESTAMP, ";
        $sql .= " PRIMARY KEY `id` (`id`) ";
        $sql .= ") ";

        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

        dbDelta( $sql );

    }

    public function deactivateDbPhonePlugin()
    {
        $sql = "DROP TABLE IF EXISTS {$this->phoneTable}";
        $this->wpdb->query($sql);
    }

    public function removePhoneshortcode()
    {
        remove_shortcode('stary_telefon');
    }


    public function fn_stary_telefon()
    {

        $html = null;
        $html .= "<form id='F_form' name='F_form' action='#'>";
        $html .= "<input id='F_letters' type='text' name='letters'>";
        $html .= "<input id='submit' name='submit' type='submit' value='Send'>";
        $html .= "</form>";

        $html .= "<div class='telefon-response'></div>";

        return $html;
    }

    public function callbackResponse($input)
    {
        $numbers = $this->lettersToNumbers($input);
        $letters = $input['input'];

        $this->saveToDb($numbers,$letters);

        return $numbers;

    }

    public function saveToDb($numbers=null,$letters=null)
    {
        $data = array('input' => $letters, 'output' => $numbers );
        $format = array( '%s', '%s');
        $this->wpdb->insert($this->phoneTable,$data,$format);
    }

    public function lettersToNumbers($input)
    {

        $tmp = str_split($input['input']);

        $ret = null;
        foreach ($tmp as $k=>$v){
            $ret .= $this->convertLetterToNumber($v);
        }

        return $ret;
    }

    public function enqueueScript(){
        wp_enqueue_script( 'js-phone', plugin_dir_url(__FILE__).'/telefon.js', array('jquery'), microtime() );
    }

    public function enqueueStyle(){

        wp_enqueue_style( 'css-phone', plugin_dir_url(__FILE__).'/telefon.css', array('twenty-twenty-one-style'), microtime() );
    }

    public function convertLetterToNumber($a)
    {
        if( !preg_match("/[a-zA-Z\-]/",$a) ) return null;

        $a = strtolower($a);

        $klawisz =[
            '-'=>1,
            'a' => 2, 'b'=>2, 'c'=>2,
            'd' => 3, 'e'=>3, 'f'=>3,
            'g' => 4, 'h'=>4, 'i'=>4,
            'j' => 5, 'k'=>5, 'l'=>5,
            'm' => 6, 'n'=>6, 'o'=>6,
            'p' => 7, 'q'=>7, 'r'=>7, 's'=>7,
            't' => 8, 'u'=>8, 'v'=>8,
            'w' => 9, 'x'=>9, 'y'=>9, 'z'=>9
        ];

        $krotnosc =[
            '-'=>1,
            'a' => 1, 'b'=>2, 'c'=>3,
            'd' => 1, 'e'=>2, 'f'=>3,
            'g' => 1, 'h'=>2, 'i'=>3,
            'j' => 1, 'k'=>2, 'l'=>3,
            'm' => 1, 'n'=>2, 'o'=>3,
            'p' => 1, 'q'=>2, 'r'=>3, 's'=>4,
            't' => 1, 'u'=>2, 'v'=>3,
            'w' => 1, 'x'=>2, 'y'=>3, 'z'=>4
            ];

        return str_repeat($klawisz[$a],$krotnosc[$a]);
    }

    public function convertNumberToLetter($n)
    {
        /*
         * TODO
         * nie zrobiona konwersja odwrotna
         */
        return 'a';
    }

}
