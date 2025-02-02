<?php 
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get cart items
$cart_query = "SELECT c.*, p.name, p.price 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id";
$cart_result = mysqli_query($conn, $cart_query);

$subtotal = 0;
$delivery_fee = 5.00;

while ($item = mysqli_fetch_assoc($cart_result)) {
    $subtotal += $item['price'] * $item['quantity'];
}

$total = $subtotal + $delivery_fee;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);
    $order_notes = mysqli_real_escape_string($conn, $_POST['order_notes']);
    
    // Create order
    $order_query = "INSERT INTO orders (user_id, total_amount, payment_method, delivery_address, order_notes) 
                   VALUES ($user_id, $total, '$payment_method', '$delivery_address', '$order_notes')";
    
    if (mysqli_query($conn, $order_query)) {
        $order_id = mysqli_insert_id($conn);
        
        // Move cart items to order items
        $cart_items_query = "SELECT c.*, p.price 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id = $user_id";
        $cart_items = mysqli_query($conn, $cart_items_query);
        
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            $order_items_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                 VALUES ($order_id, $product_id, $quantity, $price)";
            mysqli_query($conn, $order_items_query);
        }
        
        // Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
        
        // Redirect to payment page for card payments
        if ($payment_method == 'card') {
            header("location: payment.php?order_id=$order_id");
            exit();
        }
        
        // Redirect to order confirmation for cash payments
        $_SESSION['success'] = "Order placed successfully!";
        header("location: order_confirmation.php?order_id=$order_id");
        exit();
    }
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Checkout</h3>
                    <form action="checkout.php" method="POST" id="checkoutForm">
                        <h5 class="mb-3">Delivery Address</h5>
                        <div class="mb-3">
                            <textarea name="delivery_address" class="form-control" rows="3" required><?php echo $user['address']; ?></textarea>
                        </div>

                        <h5 class="mb-3">Payment Method</h5>
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="cash" id="cashPayment" checked>
                                <label class="form-check-label" for="cashPayment">
                                    Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="card" id="cardPayment">
                                <label class="form-check-label" for="cardPayment">
                                    Credit/Debit Card
                                </label>
                            </div>
                        </div>

                        <h5 class="mb-3">Order Notes (Optional)</h5>
                        <div class="mb-3">
                            <textarea name="order_notes" class="form-control" rows="3" placeholder="Special instructions for delivery"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <hr>
                    <?php 
                    mysqli_data_seek($cart_result, 0);
                    while ($item = mysqli_fetch_assoc($cart_result)) { 
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?php echo $item['name']; ?> × <?php echo $item['quantity']; ?></span>
                        <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    <?php } ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee</span>
                        <span>$<?php echo number_format($delivery_fee, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </div>
                    <button type="submit" form="checkoutForm" class="btn btn-danger w-100">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>

<?php include 'includes/footer.php'; ?>
