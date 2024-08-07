<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Beautiful Home Page</title>
    <link rel="stylesheet" href="/styles/main.css">
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
                    <a href="Login/logout">Logout</a>
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
        <a>Welcome to the Site <?php echo $User ?></a>
        <h2>List of Products</h2>
        <button class="add-product-btn">Add Product</button>
        <button class="bill-product-btn">Bill Product</button>

        <?php if(isset($products) && $products != null): ?> 
<ul class="product-list"></ul>
        <?php foreach($products as $product_name => $data): ?>
        <li><?php  echo $data["productName"]  ?></li>
        <?php endforeach; ?>
        <!-- Add more products here -->
        </ul>
        <?php endif; ?>

        <?php if(isset($variable)): ?> 
<?php switch ($variable): 
 case 'value1': ?>        <p>Value 1 is selected</p>

        <?php break; ?>
        <?php case 'value2' : ?>        <p>Value 2 is selected</p>

        <?php break; ?>
        <?php default: ?>
        <p>None of the predefined values are selected</p>

        <?php endswitch; ?>;
        <?php endif; ?>
    </main>
</body>

</html>