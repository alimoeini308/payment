# What have I done?

## Strategy Design Pattern
- I Created an interface into Services/Gateways/Contracts as Gateway.php.
- I Created 3 Classes for every gateway into Services/Gateways.
- I Created a PaymentGatewayResult.php into Services/Gateways/Contracts this use for returns data into Gateway payment methode.
- I Created a VerifyGatewayResult.php into Services/Gateways/Contracts this use for returns data into Gateway verify methode.
- I Created a GatewayManager into Services/Gateways/Contracts this use for GatewayServiceProvider.
- This structure able me to use Gateway Classes and methode in controllers like this:
<br>
`$gateway = app(GatewayManager::class)->resolve($request->get('gateway', config('gateways.default')));`

## Gateways Development
- I Developed all of 3 gateways: zarinpal,shepa,zibal
- Create, Verify, Reverse for all o these gateways developed.
- Created multi transaction when amount bigger than maximum payment.

## Databases and Data Storing
- I use sqlite for save data.
- I create 2 table:
  - Payments:
    - It stores payment and has many transaction relation
  - Transactions
    - It stores transaction gateways and them statuses, it belongs to payment

## Now We Have
- Admin can crete payment for user with /api/admin/v1/payments/create
  - Admin should send user data as username and phone number to create a payment
  - If Admin send <b>amount</b> transaction create additionally.
  - If Admin doesn't send <b>amount</b> parameter a payment create without transaction
  - Admin can send link of payment for clients by payment_link in Payment model.
- Client can see payment with /api/client/v1/payment/{payment}
  - If the payment doesn't have transaction this route accept <b>amount</b> param in query params.
  - Now user can click on transactions' link and open gateway transaction.
- Transactions redirect to /api/client/v1/payments/verify
  - It verifies transaction and update transaction status
  - If Payment amount has amount that bigger than maximum amount of payments It creates next transaction.
    -  If next_transaction was null it means user complete transactions for payment.
  - It shows payment additionally, it has total_paid field that front can compare it with amount.
- Admin could have list of payments with /api/admin/v1/payments by pagination
- Admin could reverse payment transactions by /api/admin/v1/payments/reverse

# Configs
### I Created gateways.php in configs directory their variables could be change from .env file like this:
    #You can set enables gateways or disable some one
    GATEWAYS = zarinpal,shepa,zibal
    
    #Set default gateway
    DEFAULT_GATEWAY = zarinpal
    
    #Set maximum amount of payment in one session gateway(eg. 100,000 toman)
    MAX_AMOUNT = 100000


## This project dockerize additionally.

## TODO Tasks
- Develop <b>reverse</b> methode in Services/Gateways/Zibal.php when it would be available.
