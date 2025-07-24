<?php
include 'config.php';
$result = $conn->query("SELECT * FROM games");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🎮 Game Store - Premium Gaming Experience</title>
  <meta name="description" content="Discover the best games at unbeatable prices. Your premium gaming destination.">
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Header -->
  <header id="header">
    <div class="container">
      <h1>
        <i class="fas fa-gamepad"></i>
        GameVault
      </h1>
      <nav>
        <a href="index.php">
          <i class="fas fa-home"></i>
          Home
        </a>
        <a href="cart.php">
          <i class="fas fa-shopping-cart"></i>
          Cart
          <span class="cart-count" id="cartCount">0</span>
        </a>
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="profile.php">
            <i class="fas fa-user"></i>
            Profile
          </a>
          <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </a>
        <?php else: ?>
          <a href="login.php">
            <i class="fas fa-sign-in-alt"></i>
            Login
          </a>
        <?php endif; ?>
        <a href="admin/login.php">
          <i class="fas fa-cog"></i>
          Admin
        </a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Welcome to GameVault</h1>
        <p>Discover the ultimate gaming experience with our curated collection of premium games</p>
        <a href="#games" class="btn hero-btn">
          <i class="fas fa-play"></i>
          Explore Games
        </a>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <main class="container" id="games">
    <h2>
      <i class="fas fa-fire"></i>
      Featured Games
    </h2>
    
    <!-- Search and Filter -->
    <div class="controls">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search games..." />
      </div>
      <div class="sort-box">
        <select id="sortSelect">
          <option value="name">Sort by Name</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
        </select>
      </div>
    </div>

    <div class="games-grid" id="gamesGrid">
      <?php while($row = $result->fetch_assoc()): ?>
      <div class="game-card" data-name="<?= strtolower(htmlspecialchars($row['title'])) ?>" data-price="<?= $row['price'] ?>">
        <div class="game-image">
          <?php if(!empty($row['image']) && file_exists($row['image'])): ?>
            <img src="<?= htmlspecialchars($row['image']) ?>" 
                 alt="<?= htmlspecialchars($row['title']) ?>" 
                 loading="lazy">
          <?php else: ?>
            <img src="https://picsum.photos/400/240?random=<?= $row['id'] ?>" 
                 alt="<?= htmlspecialchars($row['title']) ?>" 
                 loading="lazy">
          <?php endif; ?>
          <div class="game-overlay">
            <i class="fas fa-play"></i>
          </div>
        </div>
        
        <div class="game-info">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p class="description"><?= htmlspecialchars($row['description']) ?></p>
          
          <div class="rating">
            <div class="stars">
              <?php 
              $rating = rand(35, 50) / 10; // Random rating between 3.5 and 5.0
              for($i = 1; $i <= 5; $i++): 
                if($i <= $rating): ?>
                  <i class="fas fa-star"></i>
                <?php elseif($i - 0.5 <= $rating): ?>
                  <i class="fas fa-star-half-alt"></i>
                <?php else: ?>
                  <i class="far fa-star"></i>
                <?php endif;
              endfor; ?>
            </div>
            <span class="rating-text">(<?= rand(50, 999) ?> reviews)</span>
          </div>
          
          <div class="price-section">
            <span class="price">$<?= $row['price'] ?></span>
            <?php if(rand(0, 1)): ?>
              <span class="original-price">$<?= number_format($row['price'] * 1.3, 2) ?></span>
              <span class="discount">-23%</span>
            <?php endif; ?>
          </div>
          
          <form method="post" action="cart.php" class="add-to-cart-form">
            <input type="hidden" name="game_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn add-to-cart-btn">
              <i class="fas fa-cart-plus"></i>
              Add to Cart
            </button>
          </form>
        </div>
        
        <div class="game-badges">
          <?php if(rand(0, 2) == 0): ?>
            <span class="badge new">NEW</span>
          <?php endif; ?>
          <?php if(rand(0, 3) == 0): ?>
            <span class="badge trending">TRENDING</span>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    
    <!-- Empty state -->
    <div class="empty-state" id="emptyState" style="display: none;">
      <i class="fas fa-search"></i>
      <h3>No games found</h3>
      <p>Try adjusting your search or filter criteria</p>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>GameVault</h3>
          <p>Your premium destination for the latest and greatest games.</p>
        </div>
        <div class="footer-section">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#games">Games</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="login.php">Login</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Follow Us</h4>
          <div class="social-links">
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-discord"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> GameVault. All rights reserved. | Built with ❤️ for gamers</p>
      </div>
    </div>
  </footer>

  <!-- JavaScript -->
  <script>
    // Header scroll effect
    window.addEventListener('scroll', () => {
      const header = document.getElementById('header');
      if (window.scrollY > 100) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const gamesGrid = document.getElementById('gamesGrid');
    const emptyState = document.getElementById('emptyState');

    function filterAndSort() {
      const searchTerm = searchInput.value.toLowerCase();
      const sortBy = sortSelect.value;
      const gameCards = Array.from(document.querySelectorAll('.game-card'));

      // Filter
      const filteredCards = gameCards.filter(card => {
        const gameName = card.dataset.name;
        return gameName.includes(searchTerm);
      });

      // Sort
      filteredCards.sort((a, b) => {
        switch(sortBy) {
          case 'name':
            return a.dataset.name.localeCompare(b.dataset.name);
          case 'price-low':
            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
          case 'price-high':
            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
          default:
            return 0;
        }
      });

      // Hide all cards
      gameCards.forEach(card => card.style.display = 'none');

      if (filteredCards.length === 0) {
        emptyState.style.display = 'block';
      } else {
        emptyState.style.display = 'none';
        filteredCards.forEach(card => card.style.display = 'block');
      }
    }

    searchInput.addEventListener('input', filterAndSort);
    sortSelect.addEventListener('change', filterAndSort);

    // Add to cart animation
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const btn = this.querySelector('.add-to-cart-btn');
        btn.innerHTML = '<i class="fas fa-check"></i> Added!';
        btn.style.background = 'var(--success)';
        
        setTimeout(() => {
          btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
          btn.style.background = '';
        }, 2000);
        
        // Update cart count (placeholder)
        const cartCount = document.getElementById('cartCount');
        const currentCount = parseInt(cartCount.textContent) || 0;
        cartCount.textContent = currentCount + 1;
      });
    });

    // Smooth scroll for hero button
    document.querySelector('.hero-btn').addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('games').scrollIntoView({
        behavior: 'smooth'
      });
    });

    // Game card hover effects
    document.querySelectorAll('.game-card').forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-12px) scale(1.02)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });
  </script>
</body>
</html>