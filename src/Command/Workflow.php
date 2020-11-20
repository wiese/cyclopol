<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Workflow extends Command {
	public static $defaultName = 'app:workflow';

	protected function configure() {
		$this
			->setDescription( 'Run all Cyclopol commands in their logical order.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$output->writeln( 'Cyclopol workflow' );

		$workflows = [
			DownloadSources::$defaultName,
			ArticlesFromSources::$defaultName,
			NameStreets::$defaultName,
			GeocodeAddresses::$defaultName,
		];

		$returnCode = 0;
		foreach ( $workflows as $command ) {
			$returnCode = $this->do( $command, [], $output );
			if ( $returnCode !== 0 ) {
				break;
			}
		}

		return $returnCode;
	}

	private function do(
		string $name,
		$args = [],
		OutputInterface $out
	): int {
		$out->writeln( "<comment>Running $name</comment>" );
		return $this->getApplication()->find( $name )->run( new ArrayInput( $args ), $out );
	}
}
