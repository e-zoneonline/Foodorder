<?php include 'includes/header.php'; ?>

<div class="hero-section position-relative">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836" class="d-block w-100" alt="Food" style="height: 500px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1543353071-087092ec393a" class="d-block w-100" alt="Food" style="height: 500px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1476224203421-9ac39bcb3327" class="d-block w-100" alt="Food" style="height: 500px; object-fit: cover;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <h1 class="display-4 fw-bold animate__animated animate__fadeInDown">Delicious Food Delivered</h1>
        <p class="lead animate__animated animate__fadeInUp">Order your favorite meals from the best restaurants</p>
        <a href="menu.php" class="btn btn-danger btn-lg animate__animated animate__fadeInUp">Order Now</a>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-4">Popular Categories</h2>
    <div class="row">
        <?php
        $query = "SELECT * FROM categories LIMIT 6";
        $result = mysqli_query($conn, $query);
        while ($category = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="<?php echo $category['image'] ? $category['image'] : 'https://via.placeholder.com/300x200'; ?>" 
                     class="card-img-top" alt="<?php echo $category['name']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $category['name']; ?></h5>
                    <p class="card-text"><?php echo $category['description']; ?></p>
                    <a href="menu.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-danger">View Items</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-truck fa-3x text-danger mb-3"></i>
                <h4>Fast Delivery</h4>
                <p>We deliver your food while it's still hot and fresh</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-utensils fa-3x text-danger mb-3"></i>
                <h4>Quality Food</h4>
                <p>We partner with the best restaurants in town</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                <h4>24/7 Support</h4>
                <p>Our customer support team is always here to help</p>
            </div>
        </div>
    </div>
</div>

<style>
.hero-section {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.hero-section .position-absolute {
    z-index: 2;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.fas {
    transition: transform 0.3s;
}

.fas:hover {
    transform: scale(1.1);
}
</style>

<?php include 'includes/footer.php'; ?>
