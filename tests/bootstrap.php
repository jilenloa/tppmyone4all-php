<?php

namespace MyOne4All\Test {
    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/Server.php';
    use MyOne4All\Tests\Server;
    Server::start();

    //register_shutdown_function(function () {
        //Server::stop();
    //});
}