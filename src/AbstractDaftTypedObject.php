<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use InvalidArgumentException;

/**
* @template T as array<string, scalar|array|object|null>
* @template S as array<string, scalar|null>
*
* @template-implements DaftTypedObject<T, S>
*/
abstract class AbstractDaftTypedObject implements DaftTypedObject
{
	/**
	* @var array<int, key-of<T>>
	*/
	const TYPED_PROPERTIES = [];

	/**
	* @var array<int, key-of<T>>
	*/
	const TYPED_NULLABLE_PROPERTIES = [];

	/**
	* @param T $data
	*/
	public function __construct(array $data)
	{
		foreach ($data as $property => $value) {
			$this->$property = $value;
		}
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*
	* @return T[K]
	*/
	public function __get(string $property)
	{
		/**
		* @var T[K]
		*/
		return $this->$property;
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	* @param T[K] $value
	*/
	public function __set(string $property, $value) : void
	{
		$this->$property = $value;
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*/
	public function __isset(string $property) : bool
	{
		return isset($this->$property);
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*/
	public function __unset(string $property) : void
	{
		/**
		* @var array<int, key-of<T>>
		*/
		$nullables = static::TYPED_NULLABLE_PROPERTIES;

		if ( ! in_array(
			$property,
			$nullables,
			true
		)) {
			throw new InvalidArgumentException(sprintf(
				'%s::$%s is not nullable!',
				static::class,
				$property
			));
		}

		$this->$property = null;
	}

	/**
	* @template K as key-of<T>
	*
	* @return S
	*/
	public function jsonSerialize() : array
	{
		/**
		* @var array<int, K>
		*/
		$properties = static::TYPED_PROPERTIES;

		/**
		* @var S
		*/
		return array_combine($properties, array_map(
			[$this, 'PropertyMapperToScalarOrNull'],
			$properties
		));
	}

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
	) {
		/**
		* @var scalar|array|object|null
		*/
		$value = $value;

		if ( ! is_scalar($value) && ! is_null($value)) {
			throw new InvalidArgumentException(sprintf(
				(
					'Unsupported value %s given:' .
					' %s() only supports scalar and NULL'
				),
				gettype($value),
				__METHOD__
			));
		}

		/**
		* @var S[K]
		*/
		return $value;
	}

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
	) {
		/**
		* @var T[K]
		*/
		return $value;
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*
	* @return S[K]
	*/
	protected function PropertyMapperToScalarOrNull(string $property)
	{
		/**
		* @var T[K]
		*/
		$value = $this->$property;

		/**
		* @var S[K]
		*/
		return static::PropertyValueToScalarOrNull(
			(string) $property,
			$value
		);
	}
}