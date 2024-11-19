<?php
require "vendor/autoload.php"; 

$stripe = new \Stripe\StripeClient('sk_test_51QKH1vLLTCz7k7Qk0askQrmZ3nJuGYpERV9NwLycmfkgZGAs2PqJbTb7266NzrR3i2uGobLQ2xVeY4w699odooCr00c0OmXO4c');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_products = $_POST['products'] ?? [];

    if (!empty($selected_products)) {
        try {
            // Prepare line items for the payment link
            $line_items = [];
            foreach ($selected_products as $price_id) {
                $line_items[] = [
                    'price' => $price_id,
                    'quantity' => 1,
                ];
            }

            // Create the payment link
            $payment_link = $stripe->paymentLinks->create([
                'line_items' => $line_items,
            ]);

            // Redirect to the payment link URL
            header("Location: " . $payment_link->url);
            exit;
        } catch (Exception $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>Please select at least one product.</p>";
    }
}

// Fetch products for the form
$products = $stripe->products->all(['expand' => ['data.default_price'], 'limit' => 10])->data;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Payment Link</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .product {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100%;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .product h3 {
            margin: 10px 0;
            font-size: 1em;
        }
        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Generate Payment Link</h1>
    <form method="POST">
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?= htmlspecialchars($product->images[0] ?? 'https://via.placeholder.com/150') ?>" alt="Product Image">
                    <h3><?= htmlspecialchars($product->name) ?></h3>
                    <p><?= strtoupper($product->default_price->currency) ?> <?= number_format($product->default_price->unit_amount / 100, 2) ?></p>
                    <label>
                        <input type="checkbox" name="products[]" value="<?= htmlspecialchars($product->default_price->id) ?>"> Select
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit">Generate Payment Link</button>
    </form>
</body>
</html>
