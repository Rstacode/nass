# Nass Payment Gateway — Laravel SDK

<img width="3000" height="1674" alt="Image" src="https://github.com/user-attachments/assets/65f356bb-4f70-4613-8585-5fd4febb7672" />

Laravel SDK for the [Nass Payment Gateway](https://nass.iq) — Iraq's payment processing platform.

---

## Installation

```bash
composer require rstacode/nass
```

Laravel auto-discovers the service provider. Publish the config file:

```bash
php artisan vendor:publish --tag=nass-config
```

---

## Configuration

Add the following to your `.env` file:

```env
# Merchant credentials
NASS_USERNAME=your_merchant_username
NASS_PASSWORD=your_merchant_password

# Environment URLs
# UAT:        https://uat-gateway.nass.iq:9746
# Production: https://gateway.nass.iq:9746
NASS_BASE_URL=https://uat-gateway.nass.iq:9746

NASS_TIMEOUT=30
```

---

## Usage

### 1. Authentication

Login with your merchant credentials to receive a Bearer access token:

```php
use Nass\Facades\Nass;

// Uses credentials from config (NASS_USERNAME / NASS_PASSWORD)
$response = Nass::auth()->login();
$token = $response['access_token'];

// Or pass credentials manually
$response = Nass::auth()->login('your_username', 'your_password');
$token = $response['access_token'];

// Set the token for all subsequent requests
Nass::setToken($token);
```

---

### 2. Create a Transaction

```php
use Nass\Facades\Nass;

$response = Nass::transactions()->create([
    'orderId'         => '123456',
    'orderDesc'       => 'Purchase of electronics',
    'amount'          => 150.00,
    'currency'        => '368',       // 368 = Iraqi Dinar (IQD)
    'transactionType' => '1',
    'backRef'         => 'https://yoursite.com/payment/callback',
    'notifyUrl'       => 'https://yoursite.com/payment/notify',
]);

// Redirect the customer to complete payment
$paymentUrl = $response['data']['url'];

return redirect($paymentUrl);
```

**Response example:**

```json
{
    "success": true,
    "code": 0,
    "status_code": 200,
    "data": {
        "url": "https://3dsecure.nass.iq/gateway/{Transaction Parameters}",
        "pSign": "18f...",
        "transactionParams": {
            "TERMINAL": "<TERMINAL_ID>",
            "TRTYPE": "1",
            "AMOUNT": "150",
            "ORDER": "123456"
        }
    }
}
```

---

### 3. Check Transaction Status

> **Note:** Status checks are available within **24 hours** of transaction initiation. For long-term reference use the `rrn` field.

```php
use Nass\Facades\Nass;

$status = Nass::transactions()->checkStatus('123456');

echo $status['data']['statusMsg'];    // "Approved"
echo $status['data']['responseCode']; // "00" = success
echo $status['data']['rrn'];          // Transaction reference number
```

---

### 4. Handling Callbacks

The Nass gateway will POST the transaction result to your `notifyUrl`:

```php
// routes/web.php
Route::post('/payment/notify', [PaymentController::class, 'handleCallback']);
```

```php
// PaymentController.php
public function handleCallback(Request $request): void
{
    $data = $request->all();

    // responseCode "00" = approved
    if ($data['responseCode'] === '00' && $data['actionCode'] === '0') {
        // Payment successful — store $data['rrn'] as reference
    }
}
```

**Callback payload fields:**

| Field               | Description                                  |
|---------------------|----------------------------------------------|
| `terminal`          | Terminal ID                                  |
| `actionCode`        | `0` = success                                |
| `responseCode`      | `00` = approved                              |
| `card`              | Masked card number                           |
| `amount`            | Transaction amount                           |
| `currency`          | Currency code (368 = IQD)                    |
| `rrn`               | Reference number in acquiring bank           |
| `intRef`            | Internal reference (used for reversals)      |
| `orderId`           | Your order ID (stored 7 days only)           |
| `tranDate`          | Transaction date/time                        |
| `transactionOrigin` | `local` or `international`                   |

---

### 5. Error Handling

```php
use Nass\Facades\Nass;
use Nass\Exceptions\NassException;

try {
    Nass::setToken($token);

    $response = Nass::transactions()->create([...]);
} catch (NassException $e) {
    $statusCode = $e->getCode();
    $message    = $e->getMessage();
    $response   = $e->getResponse();
}
```

---

## UAT Test Cards

Use these cards exclusively in the UAT environment:

| PAN                | Expiry | CVV |
|--------------------|--------|-----|
| 5123450000000008   | 01/39  | 100 |

---

## API Environments

| Environment | Base URL                              |
|-------------|---------------------------------------|
| UAT         | `https://uat-gateway.nass.iq:9746`    |
| Production  | `https://gateway.nass.iq:9746`        |

---

## License

MIT
