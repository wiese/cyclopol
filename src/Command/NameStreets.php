<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataAccess\ListingRepo;
use Cyclopol\DataAccess\CachedArticleRepo;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
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
                $output->writeln( "\t" . implode( ', ', $streetNames ) . ' - ' . $article->categories );
            } else {
                $output->writeln( "\t" . '<datahole>???</datahole>' );
            }
        }
        
        return 0;
    }
}