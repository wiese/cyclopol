<?php

return [
	Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => [ 'all' => true ],
	Symfony\Bundle\TwigBundle\TwigBundle::class => [ 'all' => true ],
	Overblog\GraphQLBundle\OverblogGraphQLBundle::class => [ 'all' => true ],
	Overblog\GraphiQLBundle\OverblogGraphiQLBundle::class => [ 'dev' => true ],
	Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => [ 'all' => true ],
	Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => [ 'dev' => true, 'test' => true ],
	Nelmio\CorsBundle\NelmioCorsBundle::class => [ 'all' => true ],
];
