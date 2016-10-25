<?php

namespace SnowIO\ProductSaveMutex\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SnowIO\Lock\Api\LockService;

class ProductRepositoryWithLockingPlugin
{
    /** @var LockService */
    private $lockService;

    public function __construct(LockService $lockService)
    {
        $this->lockService = $lockService;
    }

    public function aroundSave(
        ProductRepositoryInterface $productRepository,
        callable $proceed,
        ProductInterface $product,
        $saveOptions = false
    ) {
        $lockName = $this->getLockName($product->getSku());

        if (!$this->lockService->acquireLock($lockName, 0)) {
            throw new \RuntimeException('A conflict occurred while saving the product. No changes were applied.');
        }

        try {
            return $proceed($product, $saveOptions);
        } finally {
            $this->lockService->releaseLock($lockName);
        }
    }

    private function getLockName($sku)
    {
        return "product_save.$sku";
    }
}