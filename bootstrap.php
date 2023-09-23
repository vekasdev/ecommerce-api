<?php 

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require "vendor/autoload.php";
$metadataDriver = ORMSetup::createAttributeMetadataConfiguration([__DIR__."/src/entities"],true);
$conn =  DriverManager::getConnection([
        "driver" => "pdo_mysql",
        "dbname" => "ecommerce",
        "user" => "root",
        "password" => "root"
],$metadataDriver);
$em = EntityManager::create($conn,$metadataDriver);
 return $em;