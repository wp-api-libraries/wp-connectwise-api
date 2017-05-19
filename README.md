# wp-connectwise-api
A WordPress php library for [ConnectWise API](https://developer.connectwise.com/Manage/REST)

## Usage

Here is an example on how to use:

```php
$connectwise = new ConnectWiseAPI( 'YOUR CONNECTWISE URL', 'YOUR CONNECTWISE VERSION', 'YOUR COMPANY ID', 'YOUR PUBLIC KEY', 'YOUR PRIVATE KEY' );

$tickets = $connectwise->get_tickets();
```
