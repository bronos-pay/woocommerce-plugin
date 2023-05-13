<?php

namespace BronosPay\Services;

use BronosPay\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @internal
 * @covers \BronosPay\Services\ServiceFactory
 */
class ServiceFactoryTest extends TestCase
{
    /**
     * @var ServiceFactory
     */
    private $factory;

    /**
     * @var ReflectionMethod
     */
    private $getServiceClassMethod;

    /**
     * @before
     */
    protected function setUpService()
    {
        $client = $this->createSandboxClient();

        $this->factory = new ServiceFactory($client);
    }

    /**
     * @before
     */
    public function setUpReflectors()
    {
        $serviceFactoryReflector = new ReflectionClass(ServiceFactory::class);

        $this->getServiceClassMethod = $serviceFactoryReflector->getMethod('getServiceClass');
        $this->getServiceClassMethod->setAccessible(true);
    }

    public function testGetServiceClass()
    {
        $class = $this->getServiceClassMethod->invoke($this->factory, 'order');

        $this->assertSame($class, OrderService::class);
    }

    public function testGetServiceObject()
    {
        $object = $this->factory->__get('order');

        $this->assertSame(get_class($object), OrderService::class);
    }
}
