<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataAccess\ListingRepo;
use Cyclopol\DataAccess\CachedArticleRepo;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
use Cyclopol\GeoCoding\StreetAddressGeoCoder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\HttpClient\HttpClient;

class NameStreets extends Command {
    protected static $defaultName = 'app:name-streets';

    protected function configure() {
        $this
            ->setDescription( 'Shows street names for the latest articles.' )
            ->setHelp( 'Street names, district.' );
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ) {
        $articleRepo = new CachedArticleRepo( __DIR__ . '/../../var/cache/article' );
        $streetNameAnalyser = new StreetNameAnalyser();
        $httpClient = HttpClient::create( [
            'base_uri' => getenv( 'CYCLOPOL_BASE_URI' ),
            'headers' => [
                'User-Agent' => getenv( 'CYCLOPOL_DOWNLOAD_USER_AGENT' ),
            ],
        ] );

        $geoCoder = new StreetAddressGeoCoder(
            HttpClient::create( [
                'base_uri' => 'https://nominatim.openstreetmap.org',
                'headers' => [
                    'User-Agent' => getenv( 'CYCLOPOL_DOWNLOAD_USER_AGENT' ),
                ],
            ] )
        );

        $listingRepo = new ListingRepo( $httpClient ); // TODO work with what we have locally

        $page = 1;
        $listing = $listingRepo->getListing( $page );

        if ( !$listing ) {
            $output->writeln( "<error>could not find listing $page</error>" );
            return 1;
        }
        
        $output->writeln( "found listing $page" );

        $outputStyle = new OutputFormatterStyle( 'red', 'yellow', [ 'bold' ] );
        $output->getFormatter()->setStyle( 'datahole', $outputStyle );
        
        foreach ( $listing->getArticleTeasers() as $teaser ) {
            $output->writeln( $teaser->getLink() );

            try {
                $article = $articleRepo->getArticle( $teaser->getLink() );
            } catch( Exception $e ) {
                $output->writeln( '<error>stopping to get articles after problem: ' . $e->getMessage() . '</error>' );
                return 1;
            }
            
            $streetNames = $streetNameAnalyser->getStreetNames( $article->text );
            if ( count( $streetNames ) ) {
                foreach( $streetNames as $streetName ) {
                    // TODO ignore district ("categories") if "berlinweit" or "bezirksübergreifend")
                    
                    $output->writeln( "\t" . $streetName . ' - ' . $article->categories );
                    $coordinates = $geoCoder->getCoordinates( $streetName, $article->categories );
                    
                    // TODO ignore coordinates way outside berlin (maybe even in the geoCoder), e.g.
                    // Stettiner Straße - Mitte
                    // = Stettiner Straße, Mitte, Dülmen, Kreis Coesfeld, Regierungsbezirk Münster, Nordrhein-Westfalen, 48249, Deutschland
                    
                    if ( $coordinates ) {
                        $output->writeln( "\t" . $coordinates );
                    } else {
                        $output->writeln( "\t<datahole>???</datahole>" );
                    }
                    usleep( 200000 );
                }
            } else {
                $output->writeln( "\t" . '<datahole>???</datahole>' );
            }
        }
        
        return 0;
    }
}
