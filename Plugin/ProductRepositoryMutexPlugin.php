<?php

namespace SnowIO\Mutexes\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SnowIO\Lock\Api\LockService;

class ProductRepositoryMutexPlugin
{
    private $lockService;
    private $lockWaitTimeout;

    public function __construct(LockService $lockService, int $lockWaitTimeout)
    {
        $this->lockService = $lockService;
        $this->lockWaitTimeout = $lockWaitTimeout;
    }

    public function aroundSave(
        ProductRepositoryInterface $productRepository,
        callable $proceed,
        ProductInterface $product,
        $saveOptions = false
    ) {
        $lockName = "product.{$product->getSku()}";

        if (!$this->lockService->acquireLock($lockName, $this->lockWaitTimeout)) {
            throw new \RuntimeException('A conflict occurred while saving the product. No changes were applied.');
        }

        try {
            return $proceed($product, $saveOptions);
        } finally {
            $this->lockService->releaseLock($lockName);
        }
    }
}
