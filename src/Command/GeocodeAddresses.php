<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataModel\Article;
use Cyclopol\DataModel\ArticleAddress;
use Cyclopol\DataModel\Coordinate;
use Cyclopol\GeoCoding\AddressGeoCoder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class GeocodeAddresses extends Command {
	protected static $defaultName = 'app:geocode';

	private EntityManager $entityManager;

	public function __construct( EntityManager $entityManager ) {
		$this->entityManager = $entityManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setDescription( 'Add geocoordinates to article addresses.' )
			->setHelp( 'Uses a geocoder to look for street name coordinates. Checks other addresses first.' )
			->addOption(
				'throttling',
				null,
				InputOption::VALUE_OPTIONAL,
				'By how many microseconds to throttle consecutive HTTP requests',
				2000000,
			)
			->addOption(
				'user-agent',
				null,
				InputOption::VALUE_OPTIONAL,
				'HTTP user agent string to use when downloading',
				getenv( 'CYCLOPOL_DOWNLOAD_USER_AGENT' ),
			);
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$outputStyle = new OutputFormatterStyle( 'red', 'yellow', [ 'bold' ] );
		$output->getFormatter()->setStyle( 'datahole', $outputStyle );

		$addressRepo = $this->entityManager->getRepository( ArticleAddress::class );
		$coordinateRepo = $this->entityManager->getRepository( Coordinate::class );

		$geoCoder = new AddressGeoCoder(
			HttpClient::create( [
				'base_uri' => getenv( 'CYCLOPOL_GEOCODER_BASE_URI' ),
				'headers' => [
					'User-Agent' => $input->getOption( 'user-agent' ),
				],
			] )
		);

		$i = 0;
		foreach ( $addressRepo->findByCoordinate( null ) as $address ) {
			$output->writeln( (string)$address );

			// TODO ignore coordinates way outside the city (maybe even in the geoCoder), e.g.
			// Stettiner Straße - Mitte
			// = Stettiner Straße, Mitte, Dülmen, Kreis Coesfeld, Regierungsbezirk Münster,
			// Nordrhein-Westfalen, 48249, Deutschland

			$coordinates = $coordinateRepo->findOneByMatchingAddress( $address );
			if ( !$coordinates ) {
				$output->writeln( "\tno luck in older addresses</datahole>" );
				$coordinates = $geoCoder->getCoordinates( $address );
			} else {
				$output->writeln( "\t<info>got lucky in older addresses</info>" );
			}

			if ( !$coordinates ) {
				$output->writeln( "\t<datahole>??? from geocoder</datahole>" );
			} else {
				$output->writeln( "\tadd $coordinates" );
				$address->setCoordinate( $coordinates );
				$this->entityManager->persist( $address );
				$this->entityManager->flush(); // allow cache hits

				$output->writeln( "\t" . $coordinates );
			}

			$this->throttle( $input );

			if ( $i > 50 ) {
				break;
			}
			$i++;
		}

		if ( $i > 0 ) {
			$output->writeln( "<info>Persisted $i changes to DB.</info>" );
		} else {
			$output->writeln( '<info>Nothing to do, DB is up to date.</info>' );
		}

		return 0;
	}

	private function throttle( InputInterface $input ) {
		usleep( $input->getOption( 'throttling' ) );
	}
}
