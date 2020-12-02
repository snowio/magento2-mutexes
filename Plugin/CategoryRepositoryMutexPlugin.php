<?php

namespace SnowIO\Mutexes\Plugin;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use SnowIO\Lock\Api\LockService;

class CategoryRepositoryMutexPlugin
{
    private \SnowIO\Lock\Api\LockService $lockService;
    private int $lockWaitTimeout;

    public function __construct(LockService $lockService, int $lockWaitTimeout)
    {
        $this->lockService = $lockService;
        $this->lockWaitTimeout = $lockWaitTimeout;
    }

    public function aroundSave(
        CategoryRepositoryInterface $categoryRepository,
        callable $proceed,
        CategoryInterface $category
    ) {
        $categoryId = $category->getId();

        if ($categoryId === null) {
            return $proceed($category);
        }

        $lockName = "category.{$categoryId}";

        if (!$this->lockService->acquireLock($lockName, $this->lockWaitTimeout)) {
            throw new \RuntimeException('A conflict occurred while saving the category. No changes were applied.');
        }

        try {
            return $proceed($category);
        } finally {
            $this->lockService->releaseLock($lockName);
        }
    }
}
