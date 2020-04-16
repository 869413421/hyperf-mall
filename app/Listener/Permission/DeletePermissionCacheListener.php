<?php

declare(strict_types=1);

namespace App\Listener\Permission;

use Hyperf\Cache\Cache;
use Hyperf\Database\Model\Events\Event;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener
 */
class DeletePermissionCacheListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @Inject()
     * @var Cache
     */
    private $cache;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
//            QueryExecuted::class
        ];
    }

    public function process(object $event)
    {
        if (!$event instanceof Event)
        {
            return;
        }
//        $cacheKey = config('permission.cache.key');
//        $this->cache->delete($cacheKey);
    }
}
