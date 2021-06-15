# Installation

```bash
composer require dpdconnect/magento2-shipping-graphql
```

# Usage
This extension adds GraphQL support to the (dpdconnect/magento2-shipping)[https://github.com/dpdconnect/magento2-shipping] module.

With this extension you can fetch parcelshops and save a selected parcelshop. The following Query types and Mutations are supported:

Query type: **parcelshops**
Allows you to search for parcelshops either by postcode/country or a user-specified text.

Query type: **selectedParcelshop**
Returns the parcelshop saved in a Quote. 

Mutation type: **setParcelshop**
Save a selected parcelshop in a Quote.

## Example GraphQL queries

Fetch parcelshops by postcode and country
```graphql
query getDpdParcelshops($postcode: String, $countryId: String) {
  parcelshops(postcode: $postcode, countryId: $countryId) {
    items {
      parcelshop_id
      company
      street
      houseno
      zipcode
      city
      country
      latitude
      longitude
      opening_hours {
        open_morning
        open_afternoon
        close_morning
        close_afternoon
        weekday
        __typename
      }
      __typename
    }
    total_count
    __typename
  }
}
```

Save a selected parcelshop
```graphql
mutation saveParcelshop(
      $parcelshop_d: String!
      $company: String!
      $street: String!
      $houseno: String!
      $zipcode: String!
      $city: String!
      $country: String!
   ){
   setParcelshop(
      parcelshop_id: $parcelshop_id,
      company: $company,
      street: $street,
      houseno: $houseno,
      zipcode: $zipcode,
      city: $city,
      country: $country
   )
}
```

Fetch a parcelshop already saved in a Quote server sided
```graphql
    query getSelectedParcelshop(
        $cartId: String!
    ) {
        selectedParcelshop(
            cart_id: $cartId
        ) {
            parcelshop_id
            company
            street
            zipcode
            city
            country
            __typename
        }
    }
```