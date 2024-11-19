<?php
require "vendor/autoload.php"; 

$stripe = new \Stripe\StripeClient('sk_test_51QKH1vLLTCz7k7Qk0askQrmZ3nJuGYpERV9NwLycmfkgZGAs2PqJbTb7266NzrR3i2uGobLQ2xVeY4w699odooCr00c0OmXO4c');

$invoice_message = ''; // Placeholder for invoice messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer'] ?? '';
    $selected_products = $_POST['products'] ?? [];

    if ($customer_id && !empty($selected_products)) {
        try {
            // Step 1: Create the invoice
            $invoice = $stripe->invoices->create(['customer' => $customer_id]);

            // Step 2: Attach products as line items
            foreach ($selected_products as $product_id) {
                // Retrieve product price
                $product = $stripe->products->retrieve($product_id, ['expand' => ['default_price']]);
                $price_id = $product->default_price->id;

                // Create invoice item
                $stripe->invoiceItems->create([
                    'customer' => $customer_id,
                    'price' => $price_id,
                    'invoice' => $invoice->id,
                ]);
            }

            // Step 3: Finalize the invoice
            $finalized_invoice = $stripe->invoices->finalizeInvoice($invoice->id);

            // Get invoice details
            $hosted_invoice_url = $finalized_invoice->hosted_invoice_url;
            $invoice_pdf = $finalized_invoice->invoice_pdf;

            // Generate success message
            $invoice_message = "
                <div class='invoice-details'>
                    <h2>Invoice Created Successfully!</h2>
                    <p><a href='$hosted_invoice_url' target='_blank'>Pay Invoice</a></p>
                    <p><a href='$invoice_pdf' target='_blank'>Download Invoice PDF</a></p>
                </div>";
        } catch (Exception $e) {
            // Generate error message
            $invoice_message = "
                <div class='invoice-details error'>
                    <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                </div>";
        }
    } else {
        // Generate error message for missing selections
        $invoice_message = "
            <div class='invoice-details error'>
                <p>Please select a customer and at least one product.</p>
            </div>";
    }
}

// Fetch customers and products for the form
$customers = $stripe->customers->all(['limit' => 10])->data;
$products = $stripe->products->all(['expand' => ['data.default_price'], 'limit' => 10])->data;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
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
        form {
            max-width: 1000px;   
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
        select, input[type="checkbox"], button {
            display: block;
            margin-bottom: 15px;
        }
        select, input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
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
            display: block; 
            margin: 20px auto; 
            width: auto; 
        }
        button:hover {
            background-color: #0056b3;
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .product {
            border: 1px solid #808080;
            padding: 15px;
            border-radius: 8px;
            width: 250px;
            height: 300px;
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
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .product h3 {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .invoice-details {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .invoice-details h2 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .invoice-details p {
            margin: 10px 0;
            font-size: 1em;
        }
        .invoice-details a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .invoice-details a:hover {
            text-decoration: underline;
        }
        .invoice-details.error {
            background: #ffe6e6;
            border-color: #ff4d4d;
        }
        .invoice-details.error p {
            color: #cc0000;
        }
    </style>
</head>
<body>

<h1>Create Invoice</h1>

<!-- Display Invoice Details -->
<?= $invoice_message ?>

<form method="POST">
    <label for="customer">Select Customer:</label>
    <select id="customer" name="customer" required>
        <option value="">-- Select a Customer --</option>
        <?php foreach ($customers as $customer): ?>
            <option value="<?= htmlspecialchars($customer->id) ?>">
                <?= htmlspecialchars($customer->name ?: $customer->email) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="<?= htmlspecialchars($product->images[0] ?? 'https://via.placeholder.com/150') ?>" alt="Product Image">
                <h3><?= htmlspecialchars($product->name) ?></h3>
                <p><?= strtoupper($product->default_price->currency) ?> <?= number_format($product->default_price->unit_amount / 100, 2) ?></p>
                <label>
                    <input type="checkbox" name="products[]" value="<?= htmlspecialchars($product->id) ?>"> Select
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit">Create Invoice</button>
</form>

</body>
</html>
