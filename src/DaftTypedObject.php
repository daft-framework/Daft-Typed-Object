<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use JsonSerializable;

/**
* @template T as array<string, scalar|array|object|null>
* @template S as array<string, scalar|null>
*/
interface DaftTypedObject extends JsonSerializable
{
	/**
	* @param T $data
	*/
	public function __construct(array $data);

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*
	* @return T[K]
	*/
	public function __get(string $property);

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	* @param T[K] $value
	*/
	public function __set(string $property, $value) : void;

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*/
	public function __isset(string $property) : bool;

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*/
	public function __unset(string $property) : void;

	/**
	* @template K as key-of<T>
	*
	* @return S
	*/
	public function jsonSerialize() : array;

	/**
	* @template K as key-of<T>
	*
	* @param K $_property
	* @param T[K] $value
	*
	* @return S[K]
	*/
	public static function PropertyValueToScalarOrNull(
		string $_property,
		$value
	);

	/**
	* @template K as key-of<T>
	*
	* @param K $_property
	* @param S[K] $value
	*
	* @return T[K]
	*/
	public static function PropertyScalarOrNullToValue(
		string $_property,
		$value
	);
}
