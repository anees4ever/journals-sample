<?php
require_once(ROOT_DIR . "/envconf.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("date.timezone", $config["time_zone"]);
date_default_timezone_set($config["time_zone"]);

ob_start();
// include database and other util files
require_once(ROOT_DIR . "/system/database.php");
require_once ROOT_DIR . '/shared/utilities.php';
require_once(ROOT_DIR . "/template/template.php");

class App {
    public static $config= [];
    public static $dbObject= null;

    public static function init($config) {
        self::$config= $config;
        self::$dbObject = new Database();
        $db = self::$dbObject->getConnection($config);
        $db->exec("SET SESSION time_zone = '". $config["time_zone_value"] . "'");
    }

    public static function db() {
        return self::$dbObject->conn;
    }

    public static function run() {
        $path= $_SERVER["REQUEST_URI"];

        //add a backslash if it is missing
        if($path[0] != "/") $path= "/" . $path;
        if(self::$config['documentRoot'][0] != "/") self::$config['documentRoot']= "/" . self::$config['documentRoot'];

        //remove the path arguiments
        if(strpos($path, "?") !== false) {
            $path = substr($path, 0, strpos($path, "?"));
        }

        //strip the documentRoot path
        $root_path= substr($path, 0, strlen(self::$config['documentRoot']));

        if($root_path === self::$config['documentRoot']) {
            //extract the path arquiments
            $sub_path= substr($path, strlen(self::$config['documentRoot']), strlen($path));

            //remoev the start backslash if
            if($sub_path != "" && $sub_path[0] == "/") $sub_path= substr($sub_path, 1, strlen($sub_path));
            
            $pathSegments= explode("/", $sub_path);

            $controller= strtolower($pathSegments[0]);
            $controller = $controller == "" ? self::$config['defaultController'] : $controller;
            

            $controller_path= ROOT_DIR . "/controllers/{$controller}.php";
            if(file_exists($controller_path)) {
                
                //includes the controller and create object
                require($controller_path);

                $function= strtolower($pathSegments[1] ?? "index");
                if(method_exists($controller, $function)) {
                    $controllerObject= new $controller();
                    $controllerObject->$function();
                } else {
                    raise_error(500, "Requested action[{$function}] not found", 500);
                }

            } else {
                raise_error(500, "Requested page[{$controller}] not found", 500);
            }
        } else {
            raise_error(500, "Invalid documentRoot variable or invalid path", 500);
        }
    }

    public static function view($view_file, $arguiments= []) {
        $view_path= ROOT_DIR . "/views/{$view_file}.php";
        if(file_exists($view_path)) {
            Template::render($view_path, $arguiments);
        } else {
            raise_error(500, "Requested output[{$view_file}] not found", 500);
        }
    }
}

App::init($config);