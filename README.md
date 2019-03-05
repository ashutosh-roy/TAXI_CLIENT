![Taxi Share Banner](/banner600.jpg) 
# Taxi-Share-Client
Are you a **travel agent** or **airline**? This application will allow you to check if :taxi: **taxi shares** are available for the passengers of a specific flight.

Within your code you only need to provide two parameters: the **flight number** and the **arrival date**.

You could then say to the customer: 

*Hey, there are passengers on your flight willing to share a taxi to city upon arrival.* 
An added value at zero cost to the agency.

If no shares are found you could still tell the passenger:
*Did you know that you can offer to share a taxi to city to other passengers of your flight? Please visit http://taxi-share.caribation.com/ (free service to passengers)*

## PHP Example:
```php
    include 'partnerClient.php';
    $shares = new partnerClient();
    $result = $shares->getTaxiShares( '1231', 'XX9999');  // returns 200 if taxi share offers are found
```
Our server will return 200 if there are taxi share offers for the flight **XX9999** on the **31st of December**.

Other return codes are:
* 400 => No shares found!
* 417 => Invalid request! (such as invalid parameters)
* 500 => Unknown error

## Credentials are required
To use this application you will need user and password to our system. Please contact agent.support@taxisharers.com to tell us about your business and to obtain the credentials.

You will then enter the user and password received form us right on the top of the `partnerClient.php` file: 
```php
    class partnerClient
    {
        protected $user     = '';   // assigned by Taxi Share - please request
        protected $password = '';   // assigned by Taxi Share - please request
```
