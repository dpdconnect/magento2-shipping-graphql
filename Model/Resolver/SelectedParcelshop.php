<?php
declare(strict_types=1);

namespace DpdConnect\ShippingGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class SelectedParcelshop implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    public function __construct(
        GetCartForUser $getCartForUser
    ) {
        $this->getCartForUser = $getCartForUser;
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
        if (empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }
        $maskedCartId = $args['cart_id'];

        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

        return [
            'parcelshop_id' => $cart->getData('dpd_parcelshop_id'),
            'company' => $cart->getData('dpd_parcelshop_name'),
            'street' => $cart->getData('dpd_parcelshop_street'),
            'zipcode' => $cart->getData('dpd_parcelshop_zip_code'),
            'city' => $cart->getData('dpd_parcelshop_city'),
            'country' => $cart->getData('dpd_parcelshop_country'),
        ];
    }
}

