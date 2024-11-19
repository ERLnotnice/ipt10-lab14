<?php

require "vendor/autoload.php"; // Ensure Stripe PHP library is loaded

$stripe = new \Stripe\StripeClient('sk_test_51QKH1vLLTCz7k7Qk0askQrmZ3nJuGYpERV9NwLycmfkgZGAs2PqJbTb7266NzrR3i2uGobLQ2xVeY4w699odooCr00c0OmXO4c');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address_line1 = $_POST['address_line1'] ?? '';
    $address_line2 = $_POST['address_line2'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $country = $_POST['country'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';

    try {
        // Create customer in Stripe
        $customer = $stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $address_line1,
                'line2' => $address_line2,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'postal_code' => $postal_code,
            ],
        ]);

        // Success message
        echo "<p>Customer created successfully! Customer ID: " . htmlspecialchars($customer->id) . "</p>";
    } catch (Exception $e) {
        // Handle error
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h1>Create Customer</h1>
<form action="" method="POST">
    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" placeholder="Enter full name" required>

    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" placeholder="Enter email address" required>

    <label for="phone">Phone Number</label>
    <input type="tel" id="phone" name="phone" placeholder="Enter phone number" required>

    <label for="address_line1">Address Line 1</label>
    <input type="text" id="address_line1" name="address_line1" placeholder="Enter address line 1" required>

    <label for="address_line2">Address Line 2</label>
    <input type="text" id="address_line2" name="address_line2" placeholder="Enter address line 2 (optional)">

    <label for="city">City</label>
    <input type="text" id="city" name="city" placeholder="Enter city" required>

    <label for="state">State</label>
    <input type="text" id="state" name="state" placeholder="Enter state (optional)">

    <label for="country">Country</label>
    <select id="country" name="country" required>
        <option value="">Select country</option>
        <option value="US">United States</option>
        <option value="PH">Philippines</option>
        <option value="CA">Canada</option>
        <!-- Add more countries as needed -->
    </select>

    <label for="postal_code">Postal Code</label>
    <input type="text" id="postal_code" name="postal_code" placeholder="Enter postal code" required>

    <button type="submit">Create Customer</button>
</form>

</body>
</html>
