<?php
/**
 * @author Aaron Scherer ( aaron@undergroundelephant.com )
 * @date 2013
 * @copyright Underground Elephant
 */
namespace Uecode\Component;

// Symfony Classes
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 *
 */
class Config extends ParameterBag
{
	public function setItems( array $items, $replace = true )
	{
		foreach( $items as $key => $value )
		{
			if( !$this->has( $key ) || $replace ) {
				$this->set( $key, $value );
			}
		}
	}
}
