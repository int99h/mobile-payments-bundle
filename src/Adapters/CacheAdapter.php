<?php

namespace AnyKey\Adapters;

use AnyKey\Data\Validator\WindowsStore\CacheInterface as NeededCacheInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class CacheAdapter
 */
class CacheAdapter implements NeededCacheInterface
{
    /** @var CacheInterface */
    private $cache;

    /**
     * CacheAdapter constructor.
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @inheritDoc
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get($key)
    {
        return $this->getCache()->get($key, function (ItemInterface $item) {
            return $item->isHit() ? $item->get() : null;
        });
    }

    /**
     * @inheritDoc
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function put($key, $value, $minutes)
    {
        $this->getCache()->get($key, function (ItemInterface $item) use ($value, $minutes) {
            $item->set($value);
            if ($minutes) {
                $item->expiresAt(new \DateTime("+{$minutes} minutes"));
            }

            return $value;
        });
    }
}