<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('location: ../login.php');
    exit();
}

// Handle category deletion
if (isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];
    $delete_query = "DELETE FROM categories WHERE id = $category_id";
    mysqli_query($conn, $delete_query);
    $_SESSION['success'] = "Category deleted successfully";
    header('location: categories.php');
    exit();
}

// Handle category addition/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    
    if (isset($_POST['category_id'])) {
        // Update existing category
        $category_id = $_POST['category_id'];
        $query = "UPDATE categories SET name = '$name', description = '$description', image = '$image' 
                  WHERE id = $category_id";
        $message = "Category updated successfully";
    } else {
        // Add new category
        $query = "INSERT INTO categories (name, description, image) VALUES ('$name', '$description', '$image')";
        $message = "Category added successfully";
    }
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = $message;
        header('location: categories.php');
        exit();
    }
}

// Get all categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Get category for editing if ID is provided
$edit_category = null;
if (isset($_GET['edit'])) {
    $category_id = $_GET['edit'];
    $edit_query = "SELECT * FROM categories WHERE id = $category_id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_category = mysqli_fetch_assoc($edit_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Food Delivery Admin</title>
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
        .category-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
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
                        <a class="nav-link active" href="categories.php">
                            <i class="fas fa-list me-2"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customers.php">
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
                <div class="row">
                    <!-- Category Form -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">
                                    <?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?>
                                </h5>
                                <form action="categories.php" method="POST">
                                    <?php if($edit_category) { ?>
                                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                                    <?php } ?>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Category Name</label>
                                        <input type="text" class="form-control" name="name" 
                                               value="<?php echo $edit_category ? $edit_category['name'] : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3" required><?php 
                                            echo $edit_category ? $edit_category['description'] : ''; 
                                        ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Image URL</label>
                                        <input type="url" class="form-control" name="image" 
                                               value="<?php echo $edit_category ? $edit_category['image'] : ''; ?>" required>
                                    </div>
                                    
                                    <button type="submit" name="save_category" class="btn btn-danger">
                                        <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                                    </button>
                                    
                                    <?php if($edit_category) { ?>
                                        <a href="categories.php" class="btn btn-outline-secondary">Cancel</a>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Categories List -->
                    <div class="col-md-8">
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
                                <h5 class="card-title mb-4">Categories</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($category = mysqli_fetch_assoc($categories_result)) { ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo $category['image']; ?>" 
                                                         class="category-image rounded" 
                                                         alt="<?php echo $category['name']; ?>">
                                                </td>
                                                <td><?php echo $category['name']; ?></td>
                                                <td><?php echo $category['description']; ?></td>
                                                <td>
                                                    <a href="categories.php?edit=<?php echo $category['id']; ?>" 
                                                       class="btn btn-sm btn-primary me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="categories.php" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                        <input type="hidden" name="category_id" 
                                                               value="<?php echo $category['id']; ?>">
                                                        <button type="submit" name="delete_category" 
                                                                class="btn btn-sm btn-danger">
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
