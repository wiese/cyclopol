<?php
declare( strict_types = 1 );

namespace Cyclopol\Graphql\Type;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;

class DateTimeType extends ScalarType implements AliasedInterface {
	/**
	 * @param \DateTimeInterface $value
	 *
	 * @return string
	 */
	public function serialize( $value ) {
		return $value->format( 'Y-m-d H:i:s' );
	}

	/**
	 * @param mixed $value
	 *
	 * @return \DateTimeInterface
	 */
	public function parseValue( $value ) {
		return new DateTimeImmutable( $value );
	}

	/**
	 * @param Node $valueNode
	 *
	 * @return \DateTimeInterface
	 */
	public function parseLiteral( $valueNode, ?array $variables = null ) {
		return new DateTimeImmutable( $valueNode->value );
	}

	public static function getAliases(): array {
		return [ 'DateTime', 'Date' ];
	}
}
