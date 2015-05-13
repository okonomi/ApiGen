<?php

namespace ApiGen\Generator\Tests;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\ConditionalGeneratorInterface;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class GeneratorQueueTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var GeneratorQueueInterface
	 */
	private $generatorQueue;


	protected function setUp()
	{
		$eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class, [
			'dispatch' => ''
		]);
		$this->generatorQueue = new GeneratorQueue($eventDispatcherMock);
	}


	public function testRun()
	{
		$templateGeneratorMock = Mockery::mock(GeneratorInterface::class, [
			'generate' => file_put_contents(TEMP_DIR . '/file.txt', '...')
		]);
		$this->generatorQueue->addGenerator($templateGeneratorMock);
		$this->generatorQueue->run();

		$this->assertFileExists(TEMP_DIR . '/file.txt');
	}


	public function testAddToQueueAndGetQueue()
	{
		$templateGeneratorMock = Mockery::mock(GeneratorInterface::class);
		$this->generatorQueue->addGenerator($templateGeneratorMock);
		$this->assertCount(1, PHPUnit_Framework_Assert::getObjectAttribute($this->generatorQueue, 'generators'));
	}


	public function testGetAllowedQueue()
	{
		$templateGeneratorMock = Mockery::mock(GeneratorInterface::class);
		$this->generatorQueue->addGenerator($templateGeneratorMock);

		$templateGeneratorConditionalMock = Mockery::mock(ConditionalGeneratorInterface::class);
		$templateGeneratorConditionalMock->shouldReceive('isAllowed')->andReturn(FALSE);
		$this->generatorQueue->addGenerator($templateGeneratorConditionalMock);

		$this->assertCount(1, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getAllowedQueue'));
	}


	public function testGetStepCount()
	{
		$templateGeneratorMock = Mockery::mock(GeneratorInterface::class, StepCounterInterface::class, [
			'getStepCount' => 50
		]);
		$this->generatorQueue->addGenerator($templateGeneratorMock);

		$this->assertSame(50, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getStepCount'));
	}

}
