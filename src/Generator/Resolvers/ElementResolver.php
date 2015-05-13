<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ArrayObject;
use TokenReflection\Resolver;


class ElementResolver implements ElementResolverInterface
{

	/**
	 * @var string[]
	 */
	private $simpleTypes = [
		'boolean', 'integer', 'float', 'string', 'array', 'object', 'resource', 'callback', 'callable', 'NULL', 'false',
		'true', 'mixed'
	];

	/**
	 * @var ParserStorageInterface
	 */
	private $parserStorage;


	public function __construct(ParserStorageInterface $parserStorage)
	{
		$this->parserStorage = $parserStorage;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getClass($name, $namespace = '')
	{
		$parsedClasses = $this->parserStorage->getClasses();
		$class = $this->findElementByNameAndNamespace($parsedClasses, $name, $namespace);
		if ($class && $class->isDocumented()) {
			return $class;
		}

		return NULL;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getConstant($name, $namespace = '')
	{
		$parsedConstants = $this->parserStorage->getConstants();
		$constant = $this->findElementByNameAndNamespace($parsedConstants, $name, $namespace);
		if ($constant && $constant->isDocumented()) {
			return $constant;
		}

		return NULL;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFunction($name, $namespace = '')
	{
		$parsedFunctions = $this->parserStorage->getFunctions();
		$function = $this->findElementByNameAndNamespace($parsedFunctions, $name, $namespace);
		if ($function && $function->isDocumented()) {
			return $function;
		}

		return NULL;
	}


	/**
	 * {@inheritdoc}
	 */
	public function resolveElement($definition, $reflectionElement, &$expectedName = NULL)
	{
		if ($this->isSimpleType($definition)) {
			return NULL;
		}

		$originalContext = $reflectionElement;
		$reflectionElement = $this->correctContextForParameterOrClassMember($reflectionElement);
		if ($reflectionElement === NULL) {
			return NULL;
		}

		// self, $this references
		if ($definition === 'self' || $definition === '$this') {
			return $reflectionElement instanceof ClassReflectionInterface ? $reflectionElement : NULL;
		}

		$definitionBase = substr($definition, 0, strcspn($definition, '\\:'));
		$namespaceAliases = $reflectionElement->getNamespaceAliases();
		$className = Resolver::resolveClassFqn($definition, $namespaceAliases, $reflectionElement->getNamespaceName());

		if ($resolved = $this->resolveIfParsed($definition, $reflectionElement)) {
			return $resolved;
		}

		if ( ! empty($definitionBase) && isset($namespaceAliases[$definitionBase]) && $definition !== $className) {
			// Aliased class
			$expectedName = $className;

			if (strpos($className, ':') === FALSE) {
				return $this->getClass($className, $reflectionElement->getNamespaceName());

			} else {
				$definition = $className;
			}
		}

		if (($pos = strpos($definition, '::')) || ($pos = strpos($definition, '->'))) {
			$reflectionElement = $this->resolveContextForClassProperty($definition, $reflectionElement, $pos);
			$definition = substr($definition, $pos + 2);

		} elseif ($originalContext instanceof ParameterReflectionInterface) {
			return NULL;
		}

		if ( ! $this->isContextUsable($reflectionElement)) {
			return NULL;
		}

		return $this->resolveIfInContext($definition, $reflectionElement);
	}


	/**
	 * @param ClassReflectionInterface|ParameterReflectionInterface|FunctionReflectionInterface $reflectionElement
	 * @return ClassReflectionInterface|FunctionReflectionInterface
	 */
	private function correctContextForParameterOrClassMember($reflectionElement)
	{
		if ($reflectionElement instanceof ParameterReflectionInterface
			&& $reflectionElement->getDeclaringClassName() === NULL
		) {
			// Parameter of function in namespace or global space
			return $this->getFunction($reflectionElement->getDeclaringFunctionName());

		} elseif ($reflectionElement instanceof InClassInterface) {
			// Member of a class
			return $this->getClass($reflectionElement->getDeclaringClassName());
		}
		return $reflectionElement;
	}


	/**
	 * @param string $definition
	 * @param int $pos
	 * @param ElementReflectionInterface $reflectionElement
	 * @return ClassReflectionInterface
	 */
	private function resolveContextForSelfProperty($definition, $pos, ElementReflectionInterface $reflectionElement)
	{
		$class = $this->getClass(substr($definition, 0, $pos), $reflectionElement->getNamespaceName());
		if ($class === NULL) {
			$fqnName = Resolver::resolveClassFqn(
				substr($definition, 0, $pos), $reflectionElement->getNamespaceAliases(), $reflectionElement->getNamespaceName()
			);
			$class = $this->getClass($fqnName);
		}
		return $class;
	}


	/**
	 * @param string $definition
	 * @return bool
	 */
	private function isSimpleType($definition)
	{
		if (empty($definition) || in_array($definition, $this->simpleTypes)) {
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * @param string $definition
	 * @param ElementReflectionInterface $reflectionElement
	 * @return ClassReflectionInterface|ConstantReflectionInterface|FunctionReflectionInterface|NULL
	 */
	private function resolveIfParsed($definition, ElementReflectionInterface $reflectionElement)
	{
		$definition = $this->removeEndBrackets($definition);
		if ($class = $this->getClass($definition, $reflectionElement->getNamespaceName())) {
			return $class;

		} elseif ($constant = $this->getConstant($definition, $reflectionElement->getNamespaceName())) {
			return $constant;

		} elseif ($function = $this->getFunction($definition, $reflectionElement->getNamespaceName())) {
			return $function;
		}
		return NULL;
	}


	/**
	 * @param $definition
	 * @param ClassReflectionInterface $context
	 * @return ConstantReflectionInterface|MethodReflectionInterface|PropertyReflectionInterface|NULL
	 */
	private function resolveIfInContext($definition, ClassReflectionInterface $context)
	{
		$definition = $this->removeEndBrackets($definition);
		$definition = $this->removeStartDollar($definition);

		if ($context->hasProperty($definition)) {
			return $context->getProperty($definition);

		} elseif ($context->hasMethod($definition)) {
			return $context->getMethod($definition);

		} elseif ($context->hasConstant($definition)) {
			return $context->getConstant($definition);
		}
		return NULL;
	}


	/**
	 * @param string $definition
	 * @return string
	 */
	private function removeEndBrackets($definition)
	{
		if (substr($definition, -2) === '()') {
			return substr($definition, 0, -2);
		}
		return $definition;
	}


	/**
	 * @param string $definition
	 * @return string
	 */
	private function removeStartDollar($definition)
	{
		if ($definition[0] === '$') {
			return substr($definition, 1);
		}
		return $definition;
	}


	/**
	 * @param string $definition
	 * @param ClassReflectionInterface $reflectionClass
	 * @param int $pos
	 * @return ClassReflectionInterface
	 */
	private function resolveContextForClassProperty($definition, ClassReflectionInterface $reflectionClass, $pos)
	{
		// Class::something or Class->something
		if (strpos($definition, 'parent::') === 0 && ($parentClassName = $reflectionClass->getParentClassName())) {
			return $this->getClass($parentClassName);

		} elseif (strpos($definition, 'self::') !== 0) {
			return $this->resolveContextForSelfProperty($definition, $pos, $reflectionClass);
		}
		return $reflectionClass;
	}


	/**
	 * @param NULL|ElementReflectionInterface $reflectionElement
	 * @return bool
	 */
	private function isContextUsable($reflectionElement)
	{
		if ($reflectionElement === NULL || $reflectionElement instanceof ConstantReflectionInterface
			|| $reflectionElement instanceof FunctionReflectionInterface
		) {
			return FALSE;
		}
		return TRUE;
	}


	/**
	 * @param array|ArrayObject $elements
	 * @param string $name
	 * @param string $namespace
	 * @return ClassReflectionInterface|NULL
	 */
	private function findElementByNameAndNamespace($elements, $name, $namespace)
	{
		$namespacedName = $namespace . '\\' . $name;
		if (isset($elements[$namespacedName])) {
			return $elements[$namespacedName];
		}

		$shortName = ltrim($name, '\\');
		if (isset($elements[$shortName])) {
			return $elements[$shortName];
		}

		return NULL;
	}

}
