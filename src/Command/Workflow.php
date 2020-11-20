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

		$returnCode = $this->do( DownloadSources::$defaultName, $input, $output, [] );

		if ( $returnCode === 0 ) {
			$returnCode = $this->do( ArticlesFromSources::$defaultName, $input, $output, [] );
		}

		if ( $returnCode === 0 ) {
			$returnCode = $this->do( NameStreets::$defaultName, $input, $output, [] );
		}

		if ( $returnCode === 0 ) {
			$returnCode = $this->do( GeocodeAddresses::$defaultName, $input, $output, [] );
		}

		return $returnCode;
	}

	private function do(
		string $name,
		InputInterface $in,
		OutputInterface $out,
		$args = []
	): int {
		$out->writeln( "<comment>Running $name</comment>" );
		$command = $this->getApplication()->find( $name );
		$greetInput = new ArrayInput( $args );
		return $command->run( $in, $out );
	}
}
