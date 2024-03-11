<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Beautiful Home Page</title>
    <link rel="stylesheet" href="Static/Styles/main.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Site Logo" class="logo-image">
            <h1><?php  echo $PageName;  ?></h1>
        </div>
        <div class="user-section">
            <div class="dropdown">
                <img src="user-image.jpg" alt="User Image" class="user-image">
                <div class="dropdown-content">
                    <a href="#">Account</a>
                    <a href="logout">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn">Products</a>
                <div class="dropdown-content">
                    <a href="#">Product 1</a>
                    <a href="#">Product 2</a>
                    <a href="#">Product 3</a>
                </div>
            </li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
    <main>
        <h2>List of Products</h2>
        <button class="add-product-btn">Add Product</button>
        <button class="bill-product-btn">Bill Product</button>
        <ul class="product-list">
            <li>Product 1</li>
            <li>Product 2</li>
            <li>Product 3</li>
            <!-- Add more products here -->
        </ul>
    </main>
</body>
</html>
