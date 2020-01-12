<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\Crawler\ArticleCrawler;
use Cyclopol\DataModel\Article;
use Cyclopol\DataModel\ArticleSource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArticlesFromSources extends Command {
	protected static $defaultName = 'app:articles-from-sources';

	private EntityManager $entityManager;

	public function __construct( EntityManager $entityManager ) {
		$this->entityManager = $entityManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setDescription( 'Extracts basic information from article sources.' )
			->setHelp( 'Date, headline, text, ...' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$articleSourceRepo = $this->entityManager->getRepository( ArticleSource::class );

		$outputStyle = new OutputFormatterStyle( 'red', 'yellow', [ 'bold' ] );
		$output->getFormatter()->setStyle( 'datahole', $outputStyle );

		foreach ( $articleSourceRepo->findAll() as $articleSource ) {
			$output->writeln( $articleSource->getLink() );

			$crawler = new ArticleCrawler( $articleSource->getSource() );

			$article = new Article(
				$articleSource->getLink(),
				$crawler->getId(),
				$crawler->getPreviousIds(),
				$crawler->getTitle(),
				$crawler->getText(),
				$crawler->getTime(),
				$crawler->getCategories(),
			);

			if ( $article->getReportId() ) {
				$output->writeln( "\t" . $article->getReportId() );
			} else {
				$output->writeln( "\t<datahole>unknown report id</datahole>" );
			}
			$output->writeln( "\t" . $article->getTitle() );
			$output->writeln( "\t" . $article->getDistricts() );

			$this->entityManager->persist( $article );
		}

		$this->entityManager->flush();

		return 0;
	}
}
