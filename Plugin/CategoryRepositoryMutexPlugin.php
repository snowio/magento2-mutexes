<?php

namespace SnowIO\CategorySaveMutex\Plugin;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use SnowIO\Lock\Api\LockService;

class CategoryRepositoryMutexPlugin
{
    private $lockService;
    private $lockWaitTimeout;

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
        $lockName = "category.{$category->getId()}";

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
