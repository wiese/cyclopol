<?php
declare( strict_types = 1 );

namespace Cyclopol\Command;

use Cyclopol\DataAccess\HttpListingRepo;
use Cyclopol\DataAccess\HttpArticleSourceRepo;
use Cyclopol\DataModel\ArticleSource;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class DownloadArticles extends Command {
    protected static $defaultName = 'app:download-articles';

    private EntityManager $entityManager;
    
    public function __construct( EntityManager $entityManager ) {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure() {
        $this
            ->setDescription( 'Download the latest articles.' )
            ->setHelp( 'Saves them to db for later use' )
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
        
        $listingRepo = new HttpListingRepo( $httpClient, $baseUri ); // TODO pass url configuration, year in particular!
        $dbArticleSourceRepo = $this->entityManager->getRepository( ArticleSource::class );
        $httpArticleSourceRepo = new HttpArticleSourceRepo( $cachingHttpClient );

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
                $output->writeln( "inspecting article link {$teaser->getLink()}" );
                
                if ( $dbArticleSourceRepo->findOneByLink( $teaser->getLink() ) ) {
                    // on cancelled previous runs this may result in unreachable entries
                    $output->writeln( "<info>reached known article. looking no further</info>" );
                    return 0;
                }

                $output->writeln( "\tdownloading..." );

                try {
                    $articleSource = $httpArticleSourceRepo->get( $teaser->getLink() );

                    $this->entityManager->persist( $articleSource );
                    $this->entityManager->flush(); // saving one at a time to be sure
                } catch( Exception $e ) {
                    $output->writeln( '<error>stopping to get articles after problem: ' . $e->getMessage() . '</error>' );
                    return 1;
                }
                
                $this->throttle( $input );
            }

            $this->throttle( $input );
        } while ( true );

        return 0;
    }

    private function throttle( InputInterface $input ) {
        usleep( $input->getOption( 'throttling' ) );
    }
}
