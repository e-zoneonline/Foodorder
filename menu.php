<?php 
include 'includes/header.php';

$category_id = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where_clause = "WHERE 1=1";
if ($category_id) {
    $where_clause .= " AND category_id = $category_id";
}
if ($search) {
    $where_clause .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          $where_clause";
$result = mysqli_query($conn, $query);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Our Menu</h2>
        </div>
        <div class="col-md-6">
            <form class="d-flex" action="menu.php" method="GET">
                <input class="form-control me-2" type="search" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-danger" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    Categories
                </div>
                <div class="list-group list-group-flush">
                    <a href="menu.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                        All Categories
                    </a>
                    <?php
                    $cat_query = "SELECT * FROM categories";
                    $cat_result = mysqli_query($conn, $cat_query);
                    while ($category = mysqli_fetch_assoc($cat_result)) {
                        $active = $category_id == $category['id'] ? 'active' : '';
                        echo "<a href='menu.php?category={$category['id']}' class='list-group-item list-group-item-action {$active}'>";
                        echo $category['name'];
                        echo "</a>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($product = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo $product['image'] ? $product['image'] : 'https://via.placeholder.com/300x200'; ?>" 
                             class="card-img-top" alt="<?php echo $product['name']; ?>" 
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text text-muted"><?php echo $product['category_name']; ?></p>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <h6 class="card-subtitle mb-2">$<?php echo number_format($product['price'], 2); ?></h6>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <?php if ($product['is_available']) { ?>
                                <form action="cart_actions.php" method="POST" class="d-flex">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="10" class="form-control me-2">
                                    <button type="submit" class="btn btn-danger">Add to Cart</button>
                                </form>
                            <?php } else { ?>
                                <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo "<div class='col'><p class='text-center'>No products found.</p></div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.list-group-item.active {
    background-color: #dc3545;
    border-color: #dc3545;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    color: #dc3545;
}
</style>

<?php include 'includes/footer.php'; ?>
