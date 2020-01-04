<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataAccess\ListingRepo;
use Cyclopol\DataAccess\CachedArticleRepo;
use Cyclopol\DataAccess\DispatchingArticleRepo;
use Cyclopol\DataAccess\HttpArticleRepo;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadArticles extends Command {
    protected static $defaultName = 'app:download-articles';
    
    protected function configure() {
        $this
        ->setDescription( 'Download the latest articles.' )
        ->setHelp( 'Saves them to disk for later use' );
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ) {
        $baseUri = getenv('CYCLOPOL_BASE_URI');
        $httpClientOptions = [
            'base_uri' => $baseUri,
            'headers' => [
                'User-Agent' => getenv('CYCLOPOL_DOWNLOAD_USER_AGENT'),
            ],
        ];
        
        $httpClient = HttpClient::create( $httpClientOptions );
        
        // cache is defunct as headers (counter to what can be observed in browser) contain Cache-Control: private, no-cache
        $store = new Store( __DIR__ . '/../../var/cache/http' );
        $cachingHttpClient = new CachingHttpClient( $httpClient, $store, $httpClientOptions );
        
        $listingRepo = new ListingRepo( $httpClient, $baseUri ); // TODO pass url configuration, year in particular!
        $articleRepo = new DispatchingArticleRepo(
            new CachedArticleRepo( __DIR__ . '/../../var/cache/article' ),
            new HttpArticleRepo( $cachingHttpClient )
        );
        
        $page = 0;
        $articles = [];
        do {
            $page++;
            if ( $page > 2 ) {
                return 0;
            }
            
            $listing = $listingRepo->getListing( $page );
            if ( !$listing ) {
                $output->writeln( "could not find listing $page" );
                return 1;
            }
            
            $output->writeln( "found listing $page" );
            
            foreach ( $listing->getArticleTeasers() as $teaser ) {
                try {
                    $output->writeln( "inspecting article link {$teaser->getLink()}" );
                    $article = $articleRepo->getArticle( $teaser->getLink() );
                    
                    $articles[] = $article;
                } catch( Exception $e ) {
                    $output->writeln( '<error>stopping to get articles after problem: ' . $e->getMessage() . '</error>' );
                    return 1;
                }
                
                if ( $articleRepo->lastArticleWasServedFromCache() ) {
                    // on cancelled previous runs this may result in unreachable entries
                    $output->writeln( "<info>reached known article. looking no further</info>" );
                    return 0;
                }
                
                usleep( 200000 );
            }
            
            usleep( 200000 );
        } while ( true );

        return 0;
    }
}
