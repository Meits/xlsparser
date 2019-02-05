<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 05-Feb-19
 * Time: 10:58
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
//require "classes/lib/SimpleXLSX.php";

$config = require "config/database.php";
$settings = require "config/settings.php";

$app = new \Slim\App(['settings' => $settings,'database' => $config]);

$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['database'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['db'],
        $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


//$app->get('/', 'ParserController:home');
$app->get('/', 'ParserController:parse');

$app->run();
