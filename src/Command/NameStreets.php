<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataModel\Article;
use Cyclopol\GeoCoding\StreetAddressGeoCoder;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class NameStreets extends Command {
	protected static $defaultName = 'app:name-streets';

	private EntityManager $entityManager;

	public function __construct( EntityManager $entityManager ) {
		$this->entityManager = $entityManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setDescription( 'Shows street names for the latest articles.' )
			->setHelp( 'Street names, district.' )
			->addOption(
				'throttling',
				null,
				InputOption::VALUE_OPTIONAL,
				'By how many microseconds to throttle consecutive HTTP requests',
				200000,
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
		$streetNameAnalyser = new StreetNameAnalyser();

		$geoCoder = new StreetAddressGeoCoder(
			HttpClient::create( [
				'base_uri' => 'https://nominatim.openstreetmap.org',
				'headers' => [
					'User-Agent' => $input->getOption( 'user-agent' ),
				],
			] )
		);

		$articleRepo = $this->entityManager->getRepository( Article::class );

		$outputStyle = new OutputFormatterStyle( 'red', 'yellow', [ 'bold' ] );
		$output->getFormatter()->setStyle( 'datahole', $outputStyle );

		foreach ( $articleRepo->findAll() as $article ) {
			$output->writeln( $article->getLink() );

			$streetNames = $streetNameAnalyser->getStreetNames( $article->getText() );
			if ( count( $streetNames ) ) {
				foreach ( $streetNames as $streetName ) {
					$districtBlacklist = [
						'berlinweit',
						'bezirksübergreifend'
					];
					$districts = $article->getDistricts();
					if ( in_array( $districts, $districtBlacklist ) ) {
						$districts = null;
					}
					$output->writeln( "\t" . $streetName . ' - ' . ( $districts ?? '<datahole>???</datahole>' ) );
					$coordinates = $geoCoder->getCoordinates(
						'de',
						'Berlin',
						$streetName,
						$districts
					);

					// TODO ignore coordinates way outside berlin (maybe even in the geoCoder), e.g.
					// Stettiner Straße - Mitte
					// = Stettiner Straße, Mitte, Dülmen, Kreis Coesfeld, Regierungsbezirk Münster,
					// Nordrhein-Westfalen, 48249, Deutschland

					if ( $coordinates ) {
						$output->writeln( "\t" . $coordinates );
					} else {
						$output->writeln( "\t<datahole>???</datahole>" );
					}

					$this->throttle( $input );
				}
			} else {
				$output->writeln( "\t" . '<datahole>???</datahole>' );
			}
		}

		return 0;
	}

	private function throttle( InputInterface $input ) {
		usleep( $input->getOption( 'throttling' ) );
	}
}
