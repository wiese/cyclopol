<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataModel\ArticleSource;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
use Cyclopol\GeoCoding\StreetAddressGeoCoder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManager;

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
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ) {
        $streetNameAnalyser = new StreetNameAnalyser();

        $geoCoder = new StreetAddressGeoCoder(
            HttpClient::create( [
                'base_uri' => 'https://nominatim.openstreetmap.org',
                'headers' => [
                    'User-Agent' => getenv( 'CYCLOPOL_DOWNLOAD_USER_AGENT' ),
                ],
            ] )
        );

        $output->writeln( '<error>TODO create Article Model, translate ArticleSource to Article, read from Article</error>' );
        return 1;

        $articleRepo = $this->entityManager->getRepository( Article::class );

        $outputStyle = new OutputFormatterStyle( 'red', 'yellow', [ 'bold' ] );
        $output->getFormatter()->setStyle( 'datahole', $outputStyle );
        
        foreach ( $articleRepo->findAll() as $article ) {
            $output->writeln( $article->getLink() );

            $streetNames = $streetNameAnalyser->getStreetNames( $article->getText() );
            if ( count( $streetNames ) ) {
                foreach( $streetNames as $streetName ) {
                    // TODO ignore district ("categories") if "berlinweit" or "bezirksübergreifend")

                    $district = $article->getDistrict();
                    $output->writeln( "\t" . $streetName . ' - ' . $district );
                    $coordinates = $geoCoder->getCoordinates( $streetName, $district );
                    
                    // TODO ignore coordinates way outside berlin (maybe even in the geoCoder), e.g.
                    // Stettiner Straße - Mitte
                    // = Stettiner Straße, Mitte, Dülmen, Kreis Coesfeld, Regierungsbezirk Münster, Nordrhein-Westfalen, 48249, Deutschland
                    
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
