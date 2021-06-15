<?php
declare(strict_types=1);

namespace DpdConnect\ShippingGraphQl\Model\Resolver\DataProvider;

use DpdConnect\Shipping\Helper\Data;
use DpdConnect\Shipping\Helper\DPDClient;
use DpdConnect\Shipping\Helper\DpdSettings;
use DpdConnect\Shipping\Services\GoogleMaps;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\View\Asset\Repository;

class Parcelshops
{
    /**
     * @var DpdSettings
     */
    private $dpdSettings;
    /**
     * @var DPDClient
     */
    private $dpdClient;
    /**
     * @var GoogleMaps
     */
    private $googleMaps;

    public function __construct(
        GoogleMaps $googleMaps,
        DpdSettings $dpdSettings,
        DPDClient $dpdClient
    ) {
        $this->dpdSettings = $dpdSettings;
        $this->dpdClient = $dpdClient;
        $this->googleMaps = $googleMaps;
    }

    public function getParcelshopsByPostcode($postcode, $countryId)
    {
        $mapCenter = $this->googleMaps->getGoogleMapsCenter($postcode, $countryId);
        if ($mapCenter === null) {
            return null;
        }

        return $this->getParcelshopsByCoordinates($mapCenter[0], $mapCenter[1], $countryId);
    }

    public function getParcelshopsByQuery($query, $countryId)
    {
        $mapCenter = $this->googleMaps->getGoogleMapsCenterByQuery($query);
        if ($mapCenter === null) {
            return null;
        }

        return $this->getParcelshopsByCoordinates($mapCenter[0], $mapCenter[1], $countryId);
    }

    public function getParcelshopsByCoordinates($latitude, $longitude, $countryId)
    {
        $coordinates = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'countryIso' => $countryId,
            'limit' => $this->dpdSettings->getValue(DpdSettings::PARCELSHOP_MAPS_SHOPS)
        ];

        $parcelShops = $this->dpdClient->authenticate()->getParcelshop()->getList($coordinates);
        if (! \is_array($parcelShops)) {
            throw new GraphQlInputException(__('No parcelshops found'));
        }

        $result = [];

        foreach ($parcelShops as $shop) {
            $parcelShop = [];

            $parcelShop['parcelshop_id'] = $shop['parcelShopId'];
            $parcelShop['company'] = trim($shop['company']);
            $parcelShop['street'] = $shop['street'];
            $parcelShop['houseno'] = $shop['houseNo'];
            $parcelShop['zipcode'] = $shop['zipCode'];
            $parcelShop['city'] = $shop['city'];
            $parcelShop['country'] = $shop['isoAlpha2'];
            $parcelShop['latitude'] = $shop['latitude'];
            $parcelShop['longitude'] = $shop['longitude'];

            $openingHours = [];
            if (isset($shop['openingHours']) && is_array($shop['openingHours'])) {
                foreach ($shop['openingHours'] as $openingHour) {
                    $openingHours[] = [
                        'open_morning' => $openingHour['openMorning'],
                        'close_morning' => $openingHour['closeMorning'],
                        'open_afternoon' => $openingHour['openAfternoon'],
                        'close_afternoon' => $openingHour['closeAfternoon'],
                        'weekday' => $openingHour['weekday'],
                    ];
                }
            }

            $parcelShop['opening_hours'] = $openingHours;

            $result[$shop['parcelShopId']] = $parcelShop;
        }

        return $result;
    }
}
