<?php

require "vendor/autoload.php"; // Ensure you have Stripe's PHP library installed

$stripe = new \Stripe\StripeClient('sk_test_51QKH1vLLTCz7k7Qk0askQrmZ3nJuGYpERV9NwLycmfkgZGAs2PqJbTb7266NzrR3i2uGobLQ2xVeY4w699odooCr00c0OmXO4c');

$products = $stripe->products->all();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;  
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 0 auto;
            max-width: 1200px;
        }
        .product {
            border: 1px solid #808080;
            padding: 15px;
            border-radius: 8px;
            width: 250px; /* Fixed width for uniformity */
            height: 400px; /* Fixed height for alignment */
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product:hover {
            transform: translateY(-5px);
        }
        .product img {
            max-width: 100%;
            height: 150px; /* Standardized height for images */
            object-fit: contain;
            margin-bottom: 10px;
        }
        .product h3 {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .price {
            font-weight: bold;
            color: #333;
            margin: 10px 0;
        }
        .order-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: auto; /* Ensures the button is pushed to the bottom */
        }
        .order-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Stripe Products</h1>
    <div class="products">
        <?php foreach ($products->data as $product): ?>
            <div class="product">
                <h3><?= htmlspecialchars($product->name); ?></h3>
                <?php if (!empty($product->images)): ?>
                    <img src="<?= htmlspecialchars(array_pop($product->images)); ?>" alt="<?= htmlspecialchars($product->name); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/250x150?text=No+Image" alt="No Image Available">
                <?php endif; ?>
                <p class="price">
                    <?php
                    try {
                        $price = $stripe->prices->retrieve($product->default_price);
                        echo strtoupper($price->currency) . ' ' . number_format($price->unit_amount / 100, 2);
                    } catch (Exception $e) {
                        echo "Price not available";
                    }
                    ?>
                </p>
                <button class="order-button" onclick="orderProduct('<?= $product->id; ?>')">Order</button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function orderProduct(productId) {
            alert('Order button clicked for Product ID: ' + productId);
            // Replace the alert with a redirect to your Stripe checkout or order processing logic
        }
    </script>
</body>
</html>
