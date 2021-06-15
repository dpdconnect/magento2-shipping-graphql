<?php
declare(strict_types=1);

namespace DpdConnect\ShippingGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Parcelshops implements ResolverInterface
{
    private $parcelshopsDataProvider;

    /**
     * @param DataProvider\Parcelshops $parcelshopsDataProvider
     */
    public function __construct(
        DataProvider\Parcelshops $parcelshopsDataProvider
    ) {
        $this->parcelshopsDataProvider = $parcelshopsDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['query']) && (!isset($args['postcode']) || !isset($args['countryId']))) {
            throw new GraphQlInputException(
                __('Query or address is empty.')
            );
        }

        $parcelshops = null;


        if (isset($args['query'])) {

            $countryId = $args['countryId'] ?? 'NL';

            $parcelshops = $this->parcelshopsDataProvider->getParcelshopsByQuery($args['query'], $countryId);
        }

        if (isset($args['postcode']) && isset($args['countryId'])) {
            $parcelshops = $this->parcelshopsDataProvider->getParcelshopsByPostcode($args['postcode'], $args['countryId']);
        }

        if ($parcelshops == null) {
            throw new GraphQlInputException(
                __('No address found.')
            );
        }

        $parcelshopsData = [
            'items' => $parcelshops,
            'total_count' => count($parcelshops)
        ];

        return $parcelshopsData;
    }

}

