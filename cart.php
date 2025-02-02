<?php 
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT c.*, p.name, p.price, p.image 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $user_id";
$result = mysqli_query($conn, $query);
?>

<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>
    
    <?php if(isset($_SESSION['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <?php if(mysqli_num_rows($result) > 0) { ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $total = 0;
                        while($item = mysqli_fetch_assoc($result)) {
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <div class="row mb-4 cart-item animate__animated animate__fadeIn">
                            <div class="col-md-3">
                                <img src="<?php echo $item['image'] ? $item['image'] : 'https://via.placeholder.com/150'; ?>" 
                                     class="img-fluid rounded" alt="<?php echo $item['name']; ?>">
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo $item['name']; ?></h5>
                                    <form action="cart_actions.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-link text-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <p class="text-muted mb-2">Price: $<?php echo number_format($item['price'], 2); ?></p>
                                <div class="d-flex align-items-center">
                                    <form action="cart_actions.php" method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="10" class="form-control form-control-sm" style="width: 70px;"
                                               onchange="this.form.submit()">
                                    </form>
                                    <p class="mb-0 ms-3">
                                        Subtotal: $<?php echo number_format($subtotal, 2); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery Fee</span>
                            <span>$5.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong>$<?php echo number_format($total + 5, 2); ?></strong>
                        </div>
                        <a href="checkout.php" class="btn btn-danger w-100">Proceed to Checkout</a>
                        <form action="cart_actions.php" method="POST" class="mt-2">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline-danger w-100">Clear Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Browse our menu and add some delicious items!</p>
            <a href="menu.php" class="btn btn-danger">View Menu</a>
        </div>
    <?php } ?>
</div>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>

<?php include 'includes/footer.php'; ?>
