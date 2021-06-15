<?php
declare(strict_types=1);

namespace DpdConnect\ShippingGraphQl\Model\Resolver\Parcelshops;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

class Identity implements IdentityInterface
{

    private $cacheTag = \Magento\Framework\App\Config::CACHE_TAG;

    /**
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        return [];
        $ids =  empty($resolvedData['items']) ?
            [] : [$this->cacheTag, sprintf('%s_%s', $this->cacheTag, $resolvedData['items'])];

        return $ids;
    }
}
