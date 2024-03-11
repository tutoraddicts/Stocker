<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php  echo $stocker  ?></title>
</head>

<body>
  <!-- Header -->
  <div class="header">
    <!-- Logo and Name -->
    <div>
      <img src="your-logo.png" alt="Your Logo" width="100" height="50">
      <span class="ml-2">Your E-commerce</span>
    </div>

    <!-- Search Bar -->
    <form class="form-inline" method="post">
      <input class="form-control mr-sm-2" type="search" placeholder="search" aria-label="search" name="search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>

    <!-- Cart Icon, Wishlist Icon, Profile Section -->
    <div>
      <a href="#" class="text-success mr-3"><i class="fas fa-shopping-cart"></i></a>
      <a href="#" class="text-success mr-3"><i class="fas fa-heart"></i></a>
      <a href="#" class="text-success"><i class="fas fa-user"></i></a>
    </div>
  </div>

  <?php if( isset($user) || isset($user2) ): ?> 
My name is <?php  echo $user  ?><br>
  <?php elseif( isset($condition) ): ?> 
<?php else: ?> 

  No user presented
  <?php endif; ?>
  <!-- I am searchign for <?php echo $data['Search'] ?> -->
  <!-- Bootstrap JS (optional) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>