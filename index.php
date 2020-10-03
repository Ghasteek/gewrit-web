<?php
session_start();
mb_internal_encoding("UTF-8");

function autoloadFunction($class){
    // konci nazev tridy retezcem "Kontroler"?
    if (preg_match('/Controller$/', $class)) {
        require("controllers/" . $class . ".php");
    } else {
        require("models/" . $class . ".php");
    }
}

spl_autoload_register('autoloadFunction'); // registrace "autoloadFunction" jako autoloader

//pripojeni k db, prihlasovaci udaje nastav v PasswordHandler
$login = PasswordsHandler::getLogin('db');
Db::connect($login['dbAdress'], $login['dbUsername'], $login['dbPassword'], $login['dbName']);

// vytvorim router
$router = new RouterController();
$router->process(array($_SERVER['REQUEST_URI']));

$router->showView();

?>