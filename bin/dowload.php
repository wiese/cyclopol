<?php
declare( strict_types = 1 );

require_once __DIR__ . '/../vendor/autoload.php';

use Cyclopol\DataAccess\ListingRepo;
use Cyclopol\DataAccess\CachedArticleRepo;
use Cyclopol\DataAccess\DispatchingArticleRepo;
use Cyclopol\DataAccess\HttpArticleRepo;
use Cyclopol\TextAnalysis\StreetNameAnalyser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpClient\CachingHttpClient;

$baseUri = getenv('CYCLOPOL_BASE_URI');
$httpClientOptions = [
	'base_uri' => $baseUri,
	'headers' => [
		'User-Agent' => getenv('CYCLOPOL_DOWNLOAD_USER_AGENT'),
	],
];

$httpClient = HttpClient::create( $httpClientOptions );

// cache is defunct as headers (counter to what can be observed in browser) contain Cache-Control: private, no-cache
$store = new Store( __DIR__ . '/../var/cache/http' );
$cachingHttpClient = new CachingHttpClient( $httpClient, $store, $httpClientOptions );

$listingRepo = new ListingRepo( $httpClient, $baseUri ); // TODO pass url configuration
$articleRepo = new DispatchingArticleRepo(
	new CachedArticleRepo( __DIR__ . '/../var/cache/article' ),
	new HttpArticleRepo( $cachingHttpClient )
);

$page = 0;
$articles = [];
do {
	$page++;
	if ( $page > 2 ) {
		break;
	}

	$listing = $listingRepo->getListing( $page );
	if ( !$listing ) {
		echo "could not find listing $page\n";
		break;
	}

	echo "found listing $page\n";

	$i = 0;
	foreach ( $listing->getArticleTeasers() as $teaser ) {
		try {
			echo "inspecting article link {$teaser->getLink()}\n";
			$article = $articleRepo->getArticle( $teaser->getLink() );

			$articles[] = $article;
		} catch( Exception $e ) {
			var_dump( 'stopping to get articles after problem', $e->getMessage() );
			break 2;
		}

		if ( $articleRepo->lastArticleWasServedFromCache() ) {
		    // on cancelled previous runs this may result in unreachable entries
			echo "reached known article. looking no further\n";
			break 2;
		}

		usleep( 200000 );
	}

	usleep( 200000 );
} while ( true );

// var_dump( $articles );
