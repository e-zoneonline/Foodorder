<?php 
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

// Get order details
$order_query = "SELECT o.*, u.full_name, u.email, u.phone 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);

if (mysqli_num_rows($order_result) == 0) {
    header('location: index.php');
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT oi.*, p.name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>

<div class="container my-5">
    <?php if(isset($_SESSION['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h2>Order Confirmed!</h2>
                <p class="text-muted">Order #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Order Details</h5>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                    <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Delivery Information</h5>
                    <p><strong>Name:</strong> <?php echo $order['full_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                    <p><strong>Address:</strong> <?php echo $order['delivery_address']; ?></p>
                </div>
            </div>

            <h5 class="mt-4">Order Items</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        while ($item = mysqli_fetch_assoc($items_result)) {
                            $item_total = $item['quantity'] * $item['price'];
                            $subtotal += $item_total;
                        ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item_total, 2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Delivery Fee</strong></td>
                            <td>$5.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                            <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if ($order['order_notes']) { ?>
            <div class="mt-4">
                <h5>Order Notes</h5>
                <p class="text-muted"><?php echo $order['order_notes']; ?></p>
            </div>
            <?php } ?>

            <div class="text-center mt-4">
                <a href="menu.php" class="btn btn-outline-danger me-2">Continue Shopping</a>
                <a href="orders.php" class="btn btn-danger">View All Orders</a>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background-color: #f8f9fa;
}

@media print {
    .navbar, .footer, .btn {
        display: none;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
