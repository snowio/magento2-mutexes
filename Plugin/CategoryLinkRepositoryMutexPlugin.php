<?php

namespace SnowIO\Mutexes\Plugin;

use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use SnowIO\Lock\Api\LockService;

class CategoryLinkRepositoryMutexPlugin
{
    private \SnowIO\Lock\Api\LockService $lockService;
    private int $lockWaitTimeout;

    public function __construct(LockService $lockService, int $lockWaitTimeout)
    {
        $this->lockService = $lockService;
        $this->lockWaitTimeout = $lockWaitTimeout;
    }

    public function aroundSave(
        CategoryLinkRepositoryInterface $categoryLinkRepository,
        callable $proceed,
        CategoryProductLinkInterface $link
    ) {
        $lockNames = ["category.{$link->getCategoryId()}", "product.{$link->getSku()}"];

        if (!$this->lockService->acquireLocks($lockNames, $this->lockWaitTimeout)) {
            throw new \RuntimeException('A conflict occurred while saving the link. No changes were applied.');
        }

        try {
            return $proceed($link);
        } finally {
            $this->lockService->releaseLocks($lockNames);
        }
    }
}
