<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use OutOfBoundsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Workflow extends Command {
	public static $defaultName = 'app:workflow';
	private array $workflow;

	protected function configure() {
		$this->workflow = [
			DownloadSources::$defaultName,
			ArticlesFromSources::$defaultName,
			NameStreets::$defaultName,
			GeocodeAddresses::$defaultName,
		];

		$this
			->setDescription( 'Run all Cyclopol commands in their logical order.' )
			->addOption(
				'from',
				null,
				InputOption::VALUE_OPTIONAL,
				'Name of the first command to run (i.e. skipping over some commands in the workflow)'
			);
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$output->writeln( 'Cyclopol workflow' );

		$fromCommandIndex = $this->getAndValidateFromCommandIndex( $input );
		$returnCode = 0;
		foreach ( $this->workflow as $index => $command ) {
			if ( $index < $fromCommandIndex ) {
				$output->writeln( "<comment>Skipping $command (per 'from' option)</comment>" );
				continue;
			}

			$output->writeln( "<comment>Running $command</comment>" );
			$returnCode = $this->getApplication()
				->find( $command )
				->run( new ArrayInput( [] ), $output );
			if ( $returnCode !== 0 ) {
				break;
			}
		}

		return $returnCode;
	}

	private function getAndValidateFromCommandIndex( InputInterface $input ): int {
		$fromOption = $input->getOption( 'from' );
		if ( $fromOption === null ) {
			return 0;
		}
		$fromCommandIndex = array_search( $fromOption, $this->workflow );
		if ( $fromCommandIndex === false ) {
			throw new OutOfBoundsException( "Unknown 'from' option '$fromOption'" );
		}
		return $fromCommandIndex;
	}
}
