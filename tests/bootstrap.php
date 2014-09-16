<?php

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Tystr\\', __DIR__.'/Tystr');

use Doctrine\Common\Annotations\AnnotationRegistry;
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
