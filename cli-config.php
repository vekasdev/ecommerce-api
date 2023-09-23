<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Console\Application;

// replace with file to your own project bootstrap
$em = require_once 'bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app

$commands = [
    new Commands\CreateResponseCommand(realpath(__DIR__."/src/responses"),"Response","responses")
];

$application = new Application("my app",1.3);

 ConsoleRunner::run(
    new SingleManagerProvider($em),
    $commands
);