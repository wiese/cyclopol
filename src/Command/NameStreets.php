<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataModel\Article;
use Cyclopol\DataModel\ArticleAddress;
use Cyclopol\GeoCoding\AddressGeoCoder;
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
			->setDescription( 'Shows street names for the latest articles.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$streetNameAnalyser = new StreetNameAnalyser();

		$articleRepo = $this->entityManager->getRepository( Article::class );

		$outputStyle = new OutputFormatterStyle( 'red', 'yellow', [ 'bold' ] );
		$output->getFormatter()->setStyle( 'datahole', $outputStyle );

		// TODO this hits articles w/o a street name time and again...
		$streetNameAnalyserVersion = StreetNameAnalyser::VERSION;
		$i = 0;
		foreach ( $articleRepo->findAllWithoutAddress( $streetNameAnalyserVersion ) as $article ) {
			$output->writeln( $article->getLink() );

			$streetNames = $streetNameAnalyser->getStreetNames( $article->getText() );
			if ( count( $streetNames ) ) {
				foreach ( $streetNames as $streetAddress ) {
					$districtBlacklist = [
						'berlinweit',
						'bezirksübergreifend'
					];
					$districts = $article->getDistricts();
					if ( in_array( $districts, $districtBlacklist ) ) {
						$districts = null;
					}
					$output->writeln( "\t" . $streetAddress . ' - ' . ( $districts ?? '<datahole>???</datahole>' ) );

					$address = new ArticleAddress(
						$article,
						$streetNameAnalyserVersion,
						'de',
						'Berlin',
						$districts,
						$streetAddress->getStreet(),
						$streetAddress->getNumber(),
					);

					$this->entityManager->persist( $address );
					$i++;
				}
			} else {
				$output->writeln( "\t" . '<datahole>???</datahole>' );
			}
		}

		if ( $i > 0 ) {
			$this->entityManager->flush();
			$output->writeln( "<info>Persisted $i changes to DB.</info>" );
		} else {
		   $output->writeln( '<info>Nothing to do, DB is up to date.</info>' );
		}

		return 0;
	}
}
