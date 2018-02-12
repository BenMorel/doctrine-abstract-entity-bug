<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

use Bug\{A,B,C,D,E};

require __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('sqlite::memory:');
$pdo->query('CREATE TABLE A(id INTEGER NOT NULL PRIMARY KEY, type STRING NOT NULL)');

$config = new Configuration();
$config->setAutoGenerateProxyClasses(true);
$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver([__DIR__ . '/src'], false));
$config->setProxyDir(__DIR__ . '/proxy');
$config->setProxyNamespace('Bug\Proxy');

$em = EntityManager::create(['pdo' => $pdo], $config);

$b = new B;
$d = new D;
$e = new E;

$em->persist($b);
$em->persist($d);
$em->persist($e);
$em->flush();

$entityClasses = [
    A::class,
    B::class,
    C::class,
    D::class,
    E::class
];

foreach ($entityClasses as $entityClass) {
    $entities = $em->createQueryBuilder()
        ->select('x')
        ->from($entityClass, 'x')
        ->getQuery()
        ->getResult();

    echo $entityClass, ':', PHP_EOL;

    foreach ($entities as $key => $entity) {
        echo '    ' . (is_object($entity) ? get_class($entity) : gettype($entity)), PHP_EOL;
    }
}
