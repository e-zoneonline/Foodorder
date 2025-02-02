<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('location: ../login.php');
    exit();
}

// Handle customer deletion
if (isset($_POST['delete_customer'])) {
    $user_id = $_POST['user_id'];
    $delete_query = "DELETE FROM users WHERE id = $user_id AND user_type = 'user'";
    mysqli_query($conn, $delete_query);
    $_SESSION['success'] = "Customer deleted successfully";
    header('location: customers.php');
    exit();
}

// Get all customers (excluding admins)
$customers_query = "SELECT * FROM users WHERE user_type = 'user' ORDER BY created_at DESC";
$customers_result = mysqli_query($conn, $customers_query);

// Get customer statistics
$stats_query = "SELECT 
    COUNT(*) as total_customers,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_today,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as new_this_week
    FROM users WHERE user_type = 'user'";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - Food Delivery Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .sidebar .nav-link.active {
            background-color: #dc3545;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4">
                    <i class="fas fa-utensils fa-2x text-white"></i>
                    <h5 class="text-white mt-2">Admin Panel</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-bag me-2"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-hamburger me-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-list me-2"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="customers.php">
                            <i class="fas fa-users me-2"></i> Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">Manage Customers</h2>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card bg-primary text-white">
                            <h3><?php echo $stats['total_customers']; ?></h3>
                            <p class="mb-0">Total Customers</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-success text-white">
                            <h3><?php echo $stats['new_today']; ?></h3>
                            <p class="mb-0">New Today</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-info text-white">
                            <h3><?php echo $stats['new_this_week']; ?></h3>
                            <p class="mb-0">New This Week</p>
                        </div>
                    </div>
                </div>

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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Joined Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($customer = mysqli_fetch_assoc($customers_result)) { 
                                        // Get customer's order count
                                        $customer_id = $customer['id'];
                                        $orders_query = "SELECT COUNT(*) as order_count FROM orders WHERE user_id = $customer_id";
                                        $orders_result = mysqli_query($conn, $orders_query);
                                        $order_count = mysqli_fetch_assoc($orders_result)['order_count'];
                                    ?>
                                    <tr>
                                        <td><?php echo $customer['full_name']; ?></td>
                                        <td><?php echo $customer['email']; ?></td>
                                        <td><?php echo $customer['phone']; ?></td>
                                        <td>
                                            <small><?php echo $customer['address']; ?></small>
                                        </td>
                                        <td>
                                            <?php echo date('M j, Y', strtotime($customer['created_at'])); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo $order_count; ?> orders
                                            </small>
                                        </td>
                                        <td>
                                            <a href="view_customer.php?id=<?php echo $customer['id']; ?>" 
                                               class="btn btn-sm btn-info me-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="customers.php" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                <input type="hidden" name="user_id" value="<?php echo $customer['id']; ?>">
                                                <button type="submit" name="delete_customer" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
