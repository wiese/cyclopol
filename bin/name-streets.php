<?php
declare( strict_types = 1 );

require_once __DIR__ . '/../vendor/autoload.php';

$streetNameAnalyser = new StreetNameAnalyser();
var_dump( $streetNameAnalyser->getStreetNames( $article->text ) );

