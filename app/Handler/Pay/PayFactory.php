<?php

declare(strict_types=1);

namespace App\Handler\Pay;

use Hyperf\AsyncQueue\Exception\InvalidDriverException;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class PayFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var PayInterface[]
     */
    protected $drivers = [];

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * @throws InvalidDriverException when the driver class not exist or the class is not implemented PayInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $config = $container->get(ConfigInterface::class);

        $this->configs = $config->get('pay', []);

        foreach ($this->configs as $key => $item)
        {
            $driverClass = $item['driver'];

            if (!class_exists($driverClass))
            {
                throw new InvalidDriverException(sprintf('[Error] class %s is invalid.', $driverClass));
            }

            $driver = make($driverClass, ['config' => $item]);
            if (!$driver instanceof PayInterface)
            {
                throw new InvalidDriverException(sprintf('[Error] class %s is not instanceof %s.', $driverClass, PayInterface::class));
            }

            $this->drivers[$key] = $driver;
        }
    }

    public function __get($name): PayInterface
    {
        return $this->get($name);
    }

    /**
     * @throws InvalidDriverException when the driver invalid
     */
    public function get(string $name): PayInterface
    {
        $driver = $this->drivers[$name] ?? null;
        if (!$driver || !$driver instanceof PayInterface)
        {
            throw new InvalidDriverException(sprintf('[Error]  %s is a invalid driver.', $name));
        }

        return $driver;
    }

    public function getConfig($name): array
    {
        return $this->configs[$name] ?? [];
    }
}
