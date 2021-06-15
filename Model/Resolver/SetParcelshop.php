<?php

namespace DpdConnect\ShippingGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class SetParcelshop implements ResolverInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        GetCartForUser $getCartForUser
    ) {
        $this->quoteRepository = $quoteRepository;
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


        $cart->setData('dpd_parcelshop_id', $args['parcelshop_id']);
        $cart->setData('dpd_parcelshop_name', $args['company']);
        $cart->setData('dpd_parcelshop_street', $args['street'] . ' ' . $args['houseno']);
        $cart->setData('dpd_parcelshop_zip_code', $args['zipcode']);
        $cart->setData('dpd_parcelshop_city', $args['city']);
        $cart->setData('dpd_parcelshop_country', $args['country']);

        $this->quoteRepository->save($cart);

        return true;
    }
}
