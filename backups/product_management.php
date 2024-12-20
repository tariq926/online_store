<?php
session_start();
require 'db_config.php'; // Include the database connection script

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];// Assume you handle image uploads separately

    
    // Check if quantity is set and is a valid number
    if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        $quantity = (int)$_POST['quantity']; // Cast to integer
    } else {
        echo "Error: Quantity is required and must be a valid number.";
        exit();
    }

    // Prepare and execute the insert statement
    $stmt = $pdo->prepare("INSERT INTO products (product_name, description, price_ksh, image_url, quantity_in_stock, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $description, $price, $image, $quantity]);

    echo "Product added successfully!";
}

// Handle deleting a product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);

    echo "Product deleted successfully!";
}

// Fetch all products
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }

    header {
        text-align: center;
        margin-bottom: 20px;
    }

    h1 {
        color: #333;
    }

    main {
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    section {
        margin-bottom: 20px;
    }

    h2 {
        color: #007BFF;
        border-bottom: 2px solid #007BFF;
        padding-bottom: 5px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin: 10px 0 5px;
        color: #555;
    }

    input[type="text"],
    input[type="number"],
    textarea {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        background-color: #007BFF;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #007BFF;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #e9e9e9;
    }

    img {
        border-radius: 5px;
    }

    footer {
        text-align: center;
        margin-top: 20px;
        color: #777;
    }
    </style>
</head>

<body>
    <header>
        <h1>Product Management</h1>
    </header>
    <main>
        <section>
            <h2>Add New Product</h2>
            <form method="POST" action="">
                <label for="name">Product Name:</label>
                <input type="text" name="name" required>

                <label for="description">Description:</label>
                <textarea name="description" required></textarea>

                <label for="price">Price:</label>
                <input type="number" name="price" step="0.01" required>

                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" step="1" required>

                <label for="image">Image URL:</label>
                <input type="text" name="image" required> <!-- You can implement file upload instead -->

                <button type="submit" name="add_product">Add Product</button>
            </form>
        </section>

        <section>
            <h2>Existing Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                        <th>Quantity</th>
                        <th>Reviews</th> <!-- New column for reviews -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['product_id']; ?></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo number_format($product['price_ksh'], 2); ?></td>
                        <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>" width="50"></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">Edit</a> |
                            <a href="?delete=<?php echo $product['product_id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                        <td><?php echo htmlspecialchars($product['quantity_in_stock']); ?></td>
                        <td>
                            <a href="review_product.php?product_id=<?php echo $product['product_id']; ?>">Review</a>
                            <!-- Link to review page -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
    </footer>
</body>

</html>