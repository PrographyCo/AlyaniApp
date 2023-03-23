<?php

const COOKIE_EXPIRE = 60 * 60 * 24 * 30;  //30 days by default
const COOKIE_PATH = "/";  //Avaible in whole domain
const USER_TIMEOUT = 10;
const COOKIE_TOKEN = "TOKEN";
const COOKIE_SECRET = "SECRET";
const PROJ_TITLE = "AlyaniApp";

date_default_timezone_set('Africa/Cairo');


class Config {

    //Declarations
    public $logged_in = false;
    public $userinfo = array();  //The array holding all user info
    public $error = "";
    public $allowedCountries = []; //'EG', 'SA'
    public $showErrors = false;
    public $forceLogin = true;
    public $loginURL = CP_PATH.'/main/login';
    public $con;

    public static $dbhost = "localhost"; // Host Name
    public static $dbport = "3306"; //Port
    public static $dbuser = "root"; //iqbay487_db3 // MySQL Database Username
    public static $dbpass = ""; //;QQiJrEjHDpz // MySQL Database Password
    public static $dbname = "iqbay487_db3"; // Database Name


    function __construct() {

        // Display errors
        if ($this->showErrors) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        // Database Connection
        $stmt = "mysql:dbname=".self::$dbname.";host=".self::$dbhost.";port=".self::$dbport.";charset=utf8";
        //echo $stmt;
        $this->con = new PDO($stmt, self::$dbuser, self::$dbpass);
        $this->con->exec("set names utf8");
        $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Session start and config
        session_start();

        // Login
        if(!empty($_POST['sublogin'])) $this->login($_POST['email'], $_POST['password'], $_POST['remember']??false, $_GET['goto']??'');

        // Security - Limit access to certain countries
        $this->countryAccess($this->allowedCountries);

        // Logout
        if(isset($_GET['logout'])) $this->logout();
        else $this->logged_in = $this->checkLogin();

        // Disable referer when comes from ajax
        if (!strpos($_SERVER['REQUEST_URI'], 'auth') && !strpos($_SERVER['REQUEST_URI'], '_POST') && !strpos($_SERVER['REQUEST_URI'], 'login') && !strpos($_SERVER['REQUEST_URI'], 'post')) $_SESSION['ref'] = $_SERVER['REQUEST_URI'];

        // Force login (for control panels)
        $page = array_reverse(explode('/',trim($_SERVER['REQUEST_URI'],'/?')))[0];
        if ($this->forceLogin && !$this->logged_in && $page !== 'login' && $page !== 'install' && !str_contains($_SERVER['REQUEST_URI'], 'cards')) {
            header("Location: ".$this->loginURL);
            exit();
        }


        // Check permission
        global $permid;
        if (!$this->checkPermission($permid)) die('You do not have permission to view this page');

    }

    //Check if user is logged in
    public function checkLogin() {

        if (isset($_COOKIE[COOKIE_TOKEN])) {

            $chk1 = $this->con->prepare("SELECT * FROM _users_tokens WHERE token = :token LIMIT 1");
            $chk1->bindValue("token", $_COOKIE[COOKIE_TOKEN]);
            $chk1->execute();

            if ($chk1->rowCount() > 0) {
                $userdata = $chk1->fetch(PDO::FETCH_ASSOC);
                if (password_verify($userdata['secret'], $_COOKIE[COOKIE_SECRET])) {

                    $this->userinfo = $this->con->query("SELECT user_id, name, userlevel FROM _users WHERE user_id = ".$userdata['user_id']." LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['userinfo'] = $this->userinfo;
                    $this->updateTimestamp($_SESSION['userinfo']['user_id']);
                    return true;

                }
            }
            unset($this->userinfo, $_SESSION['userinfo']);
            setcookie(COOKIE_TOKEN, "", time()+COOKIE_EXPIRE, COOKIE_PATH);
            setcookie(COOKIE_SECRET,   "", time()+COOKIE_EXPIRE, COOKIE_PATH);
            return false;
        }



        if (isset($_SESSION['userinfo'])) {

            // Check if the user is still on the system
            $chk1 = $this->con->query("SELECT user_id FROM _users WHERE user_id = ".$_SESSION['userinfo']['user_id']." AND active = 1")->fetchColumn();

            if (!$chk1) {
    
                $this->userinfo = [];
                unset($_SESSION['userinfo']);
                setcookie(COOKIE_TOKEN, "", time()+COOKIE_EXPIRE, COOKIE_PATH);
                setcookie(COOKIE_SECRET,   "", time()+COOKIE_EXPIRE, COOKIE_PATH);
                return false;

            }


            $chk2 = $this->con->prepare("SELECT user_id, name, userlevel FROM _users WHERE user_id = :user_id");
            $chk2->bindValue("user_id", $_SESSION['userinfo']['user_id']);
            $chk2->execute();

            if ($chk2->rowCount() > 0) {

                $this->userinfo = $_SESSION['userinfo'];
                $this->updateTimestamp($_SESSION['userinfo']['user_id']);
                return true;

            }
    
            $this->userinfo = [];
            unset($_SESSION['userinfo']);
            setcookie(COOKIE_TOKEN, "", time()+COOKIE_EXPIRE, COOKIE_PATH);
            setcookie(COOKIE_SECRET,   "", time()+COOKIE_EXPIRE, COOKIE_PATH);
            return false;
        }
    
    
        $this->userinfo = [];
        unset($_SESSION['userinfo']);
        setcookie(COOKIE_TOKEN, "", time()+COOKIE_EXPIRE, COOKIE_PATH);
        setcookie(COOKIE_SECRET,   "", time()+COOKIE_EXPIRE, COOKIE_PATH);
        return false;

    }

    // Login Function
    public function login($email, $password, $rememberme, $referto) {

        $this->loglogin($email, $password);

        $chk2 = $this->con->prepare("SELECT user_id, name, email, password, userlevel FROM _users WHERE email = :email LIMIT 1");
        $chk2->bindValue("email", $email);
        $chk2->execute();

        if ($chk2->rowCount() > 0) {

            $userdata = $chk2->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $userdata['password'])) {

                $this->userinfo = $userdata;
                //print_r($this->userinfo);
                //die();
                if ($rememberme) {
                    $token = $this->GUID();
                    $secret = $this->GUID();
                    $this->insertToken($this->userinfo['user_id'], $token, $secret);
                    setcookie(COOKIE_TOKEN, $token, time()+COOKIE_EXPIRE, COOKIE_PATH);
                    setcookie(COOKIE_SECRET,   password_hash($secret, PASSWORD_DEFAULT), time()+COOKIE_EXPIRE, COOKIE_PATH);
                }

                $_SESSION['userinfo'] = $this->userinfo;
                header("Location: ".CP_PATH."/main/index");
                exit();

            } else $this->error = "Incorrect login information provided";

        } else $this->error = "User not found";
    }

    function loglogin($email, $password) {

        $log = $this->con->prepare(
            "INSERT INTO _login_attempts VALUES (
                '',
                :ip,
                NOW(),
                :email,
                :password
            )");
        $log->bindValue("ip", $this->get_client_ip());
        $log->bindValue("email", $email);
        $log->bindValue("password", '');
        $log->execute();

    }

    // Logout Function
    function logout() {
        setcookie(COOKIE_TOKEN, "", time()+COOKIE_EXPIRE, COOKIE_PATH);
        setcookie(COOKIE_SECRET,   "", time()+COOKIE_EXPIRE, COOKIE_PATH);
        //$this->removeActiveUser($_SESSION['userinfo']['user_id']);
        $theref = $_SESSION['ref'];
        session_unset();
        session_destroy();
        $this->userinfo = array();
        $_SESSION['ref'] = $theref;
        header("Location: ".$_SESSION['ref']);
    }

    // Helper functions
    
    /**
     * @throws Exception
     */
    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return com_create_guid();
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', random_int(0, 65535), random_int(0, 65535), random_int(0, 65535), random_int(16384, 20479), random_int(32768, 49151), random_int(0, 65535), random_int(0, 65535), random_int(0, 65535));
    }


    function countryAccess($countries) {
        
        if (is_array($countries) && count($countries) > 0) {
            if($userIP = ip2long($this->get_client_ip())) {
                $countrycode = $this->con->query("SELECT countrycode FROM _ip2country WHERE ipfrom < $userIP AND ipto >= $userIP")->fetchColumn();
                if (!in_array($countrycode, $countries, true)) die('access denied');
            }else die('access denied');
        }
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function updateTimestamp($user_id){
        $this->con->query("UPDATE _users SET lastactive = '".time()."' WHERE user_id = $user_id");
    }

    function insertToken($user_id, $token, $secret){

        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        $query = $this->con->prepare("INSERT INTO _users_tokens VALUES (:user_id, :token, :secret, :hostname)");
        $query->bindValue("user_id", $user_id);
        $query->bindValue("token", $token);
        $query->bindValue("secret", $secret);
        $query->bindValue("hostname", $hostname);
        $query->execute();
    }

    function rand_string($length) {
        $str="";
        $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        for($i = 0;$i < $length;$i++) {
            $str .= $chars[rand(0,$size-1)];
        }
        return $str;
    }


    function checkPermission($permid) {

        if (!empty($this->userinfo) && $this->userinfo['userlevel'] === 9) return true;

        if ($permid > 0) {

            $chk = $this->con->query("SELECT permid FROM _users_perms WHERE user_id = ".$this->userinfo['user_id']." AND permid = $permid")->fetchColumn();
            if (!$chk) return false;
            else return true;

        } else return true;

    }

}

$session = new Config;

function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
        array(1 , 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
}

function getAge($birth){
    $t = time();
    $age = ($birth < 0) ? ( $t + ($birth * -1) ) : $t - $birth;
    return floor($age/31536000);
}

function containsTLD($string) {
    preg_match(
        "/(AC($|\/)|\.AD($|\/)|\.AE($|\/)|\.AERO($|\/)|\.AF($|\/)|\.AG($|\/)|\.AI($|\/)|\.AL($|\/)|\.AM($|\/)|\.AN($|\/)|\.AO($|\/)|\.AQ($|\/)|\.AR($|\/)|\.ARPA($|\/)|\.AS($|\/)|\.ASIA($|\/)|\.AT($|\/)|\.AU($|\/)|\.AW($|\/)|\.AX($|\/)|\.AZ($|\/)|\.BA($|\/)|\.BB($|\/)|\.BD($|\/)|\.BE($|\/)|\.BF($|\/)|\.BG($|\/)|\.BH($|\/)|\.BI($|\/)|\.BIZ($|\/)|\.BJ($|\/)|\.BM($|\/)|\.BN($|\/)|\.BO($|\/)|\.BR($|\/)|\.BS($|\/)|\.BT($|\/)|\.BV($|\/)|\.BW($|\/)|\.BY($|\/)|\.BZ($|\/)|\.CA($|\/)|\.CAT($|\/)|\.CC($|\/)|\.CD($|\/)|\.CF($|\/)|\.CG($|\/)|\.CH($|\/)|\.CI($|\/)|\.CK($|\/)|\.CL($|\/)|\.CM($|\/)|\.CN($|\/)|\.CO($|\/)|\.COM($|\/)|\.COOP($|\/)|\.CR($|\/)|\.CU($|\/)|\.CV($|\/)|\.CX($|\/)|\.CY($|\/)|\.CZ($|\/)|\.DE($|\/)|\.DJ($|\/)|\.DK($|\/)|\.DM($|\/)|\.DO($|\/)|\.DZ($|\/)|\.EC($|\/)|\.EDU($|\/)|\.EE($|\/)|\.EG($|\/)|\.ER($|\/)|\.ES($|\/)|\.ET($|\/)|\.EU($|\/)|\.FI($|\/)|\.FJ($|\/)|\.FK($|\/)|\.FM($|\/)|\.FO($|\/)|\.FR($|\/)|\.GA($|\/)|\.GB($|\/)|\.GD($|\/)|\.GE($|\/)|\.GF($|\/)|\.GG($|\/)|\.GH($|\/)|\.GI($|\/)|\.GL($|\/)|\.GM($|\/)|\.GN($|\/)|\.GOV($|\/)|\.GP($|\/)|\.GQ($|\/)|\.GR($|\/)|\.GS($|\/)|\.GT($|\/)|\.GU($|\/)|\.GW($|\/)|\.GY($|\/)|\.HK($|\/)|\.HM($|\/)|\.HN($|\/)|\.HR($|\/)|\.HT($|\/)|\.HU($|\/)|\.ID($|\/)|\.IE($|\/)|\.IL($|\/)|\.IM($|\/)|\.IN($|\/)|\.INFO($|\/)|\.INT($|\/)|\.IO($|\/)|\.IQ($|\/)|\.IR($|\/)|\.IS($|\/)|\.IT($|\/)|\.JE($|\/)|\.JM($|\/)|\.JO($|\/)|\.JOBS($|\/)|\.JP($|\/)|\.KE($|\/)|\.KG($|\/)|\.KH($|\/)|\.KI($|\/)|\.KM($|\/)|\.KN($|\/)|\.KP($|\/)|\.KR($|\/)|\.KW($|\/)|\.KY($|\/)|\.KZ($|\/)|\.LA($|\/)|\.LB($|\/)|\.LC($|\/)|\.LI($|\/)|\.LK($|\/)|\.LR($|\/)|\.LS($|\/)|\.LT($|\/)|\.LU($|\/)|\.LV($|\/)|\.LY($|\/)|\.MA($|\/)|\.MC($|\/)|\.MD($|\/)|\.ME($|\/)|\.MG($|\/)|\.MH($|\/)|\.MIL($|\/)|\.MK($|\/)|\.ML($|\/)|\.MM($|\/)|\.MN($|\/)|\.MO($|\/)|\.MOBI($|\/)|\.MP($|\/)|\.MQ($|\/)|\.MR($|\/)|\.MS($|\/)|\.MT($|\/)|\.MU($|\/)|\.MUSEUM($|\/)|\.MV($|\/)|\.MW($|\/)|\.MX($|\/)|\.MY($|\/)|\.MZ($|\/)|\.NA($|\/)|\.NAME($|\/)|\.NC($|\/)|\.NE($|\/)|\.NET($|\/)|\.NF($|\/)|\.NG($|\/)|\.NI($|\/)|\.NL($|\/)|\.NO($|\/)|\.NP($|\/)|\.NR($|\/)|\.NU($|\/)|\.NZ($|\/)|\.OM($|\/)|\.ORG($|\/)|\.PA($|\/)|\.PE($|\/)|\.PF($|\/)|\.PG($|\/)|\.PH($|\/)|\.PK($|\/)|\.PL($|\/)|\.PM($|\/)|\.PN($|\/)|\.PR($|\/)|\.PRO($|\/)|\.PS($|\/)|\.PT($|\/)|\.PW($|\/)|\.PY($|\/)|\.QA($|\/)|\.RE($|\/)|\.RO($|\/)|\.RS($|\/)|\.RU($|\/)|\.RW($|\/)|\.SA($|\/)|\.SB($|\/)|\.SC($|\/)|\.SD($|\/)|\.SE($|\/)|\.SG($|\/)|\.SH($|\/)|\.SI($|\/)|\.SJ($|\/)|\.SK($|\/)|\.SL($|\/)|\.SM($|\/)|\.SN($|\/)|\.SO($|\/)|\.SR($|\/)|\.ST($|\/)|\.SU($|\/)|\.SV($|\/)|\.SY($|\/)|\.SZ($|\/)|\.TC($|\/)|\.TD($|\/)|\.TEL($|\/)|\.TF($|\/)|\.TG($|\/)|\.TH($|\/)|\.TJ($|\/)|\.TK($|\/)|\.TL($|\/)|\.TM($|\/)|\.TN($|\/)|\.TO($|\/)|\.TP($|\/)|\.TR($|\/)|\.TRAVEL($|\/)|\.TT($|\/)|\.TV($|\/)|\.TW($|\/)|\.TZ($|\/)|\.UA($|\/)|\.UG($|\/)|\.UK($|\/)|\.US($|\/)|\.UY($|\/)|\.UZ($|\/)|\.VA($|\/)|\.VC($|\/)|\.VE($|\/)|\.VG($|\/)|\.VI($|\/)|\.VN($|\/)|\.VU($|\/)|\.WF($|\/)|\.WS($|\/)|\.XN--0ZWM56D($|\/)|\.XN--11B5BS3A9AJ6G($|\/)|\.XN--80AKHBYKNJ4F($|\/)|\.XN--9T4B11YI5A($|\/)|\.XN--DEBA0AD($|\/)|\.XN--G6W251D($|\/)|\.XN--HGBK6AJ7F53BBA($|\/)|\.XN--HLCJ6AYA9ESC7A($|\/)|\.XN--JXALPDLP($|\/)|\.XN--KGBECHTV($|\/)|\.XN--ZCKZAH($|\/)|\.YE($|\/)|\.YT($|\/)|\.YU($|\/)|\.ZA($|\/)|\.ZM($|\/)|\.ZW)/i",
        $string,
        $M);
    $has_tld = (count($M) > 0) ? true : false;
    return $has_tld;
}

function cleaner($url) {
    $chkurl = explode(' ', $url);
    if (count($chkurl) > 35) {
        $url = implode(' ', array_slice(explode(' ', $url), 0, 35));
        $url .= ' ... ';
    }
    $url = str_replace("\\n", " ", $url);
    $url = stripslashes($url);
    $U = explode(' ',$url);

    $W =array();
    foreach ($U as $k => $u) {
        if (stristr($u,".")) { //only preg_match if there is a dot
            if (containsTLD($u) === true) {
                unset($U[$k]);
                return cleaner( implode(' ',$U));
            }
        }
    }
    return implode(' ',$U);
}

function cleaner2($url) {
    $url = str_replace("\\n", " ", $url);
    $url = str_replace(Chr(13), "<br />", $url);
    $url = stripslashes($url);
    $U = explode(' ',$url);

    $W =array();
    foreach ($U as $k => $u) {
        if (stristr($u,".")) { //only preg_match if there is a dot
            if (containsTLD($u) === true) {
                unset($U[$k]);
                return cleaner2( implode(' ',$U));
            }
        }
    }
    return implode(' ',$U);
}



function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

function fixstr($str) {

    return trim(mysql_real_escape_string($str));

}

function reporterrortoadmin($page, $error) {

    mail('emad.fankoosh@gmail.com', PROJ_TITLE, "Page: ".$page."\n".$error);

}

function addlog($user_id, $shift_id, $action, $title, $link) {

    $dbhost = Config::$dbhost; // Host Name
    $dbport = Config::$dbport; //Port
    $dbuser = Config::$dbuser; // MySQL Database Username
    $dbpass = Config::$dbpass; // MySQL Database Password
    $dbname = Config::$dbname; // Database Name
    
    $db = new PDO("mysql:dbname={$dbname};host={$dbhost};port={$dbport};charset=utf8", $dbuser, $dbpass);
    $db->exec("set names utf8");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {

        $sql = $db->prepare("INSERT INTO sys_logs VALUES (
		'',
		:user_id,
		:shift_id,
		:action,
		:title,
		:link,
		:dateadded
		)");

        $sql->bindValue("user_id", $user_id);
        $sql->bindValue("shift_id", $shift_id);
        $sql->bindValue("action", $action);
        $sql->bindValue("title", $title);
        $sql->bindValue("link", $link);
        $sql->bindValue("dateadded", time());

        $sql->execute();

    } catch (PDOException $e) {}

}
?>
