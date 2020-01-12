<?php
declare( strict_types = 1 );

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once 'vendor/autoload.php';

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(
	[ __DIR__ . '/src' ],
	$isDevMode,
	$proxyDir,
	$cache,
	$useSimpleAnnotationReader,
);

// database configuration parameters
$conn = [
	'driver' => 'pdo_mysql',
	'server_version' => 'mariadb-10.4.11',

	'user' => $_ENV[ 'MYSQL_USER' ],
	'password' => $_ENV[ 'MYSQL_PASSWORD' ],
	'host' => $_ENV[ 'MYSQL_HOST' ],
	'dbname' => $_ENV[ 'MYSQL_DATABASE' ],
];

// obtaining the entity manager
$entityManager = EntityManager::create( $conn, $config );
