<?php
declare( strict_types = 1 );

require_once 'bootstrap.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(
	$entityManager
);
