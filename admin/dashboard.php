<?php
include '../config.php';
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

$result = $conn->query("SELECT * FROM games ORDER BY id DESC");
$total_games = $conn->query("SELECT COUNT(*) as count FROM games")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GameVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .admin-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .admin-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .admin-nav a {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--gray-light);
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-nav a:hover, .admin-nav a.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }
        
        .stat-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: var(--gray);
            font-weight: 500;
        }
        
        .games-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .section-header h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .section-header h2::after {
            display: none;
        }
        
        .add-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--success), #059669);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .game-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--gray-light);
            border-radius: var(--radius);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .game-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        
        .game-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, var(--gray-light), var(--gray));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .game-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .game-image .no-image {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .game-image .no-image i {
            font-size: 2rem;
        }
        
        .game-info {
            padding: 1.5rem;
        }
        
        .game-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .game-description {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .game-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .game-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            flex: 1;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }
        
        .edit-btn {
            background: var(--warning);
            color: var(--white);
        }
        
        .edit-btn:hover {
            background: #d97706;
            transform: translateY(-1px);
        }
        
        .delete-btn {
            background: var(--error);
            color: var(--white);
        }
        
        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .search-box {
            position: relative;
            max-width: 400px;
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid var(--gray-light);
            border-radius: var(--radius);
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 0 1rem;
            }
            
            .admin-nav {
                flex-direction: column;
                align-items: center;
            }
            
            .admin-nav a {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .games-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>
                <i class="fas fa-tachometer-alt"></i>
                Admin Dashboard
            </h1>
            <p>Manage your game store with ease</p>
        </div>
        
        <div class="admin-nav">
            <a href="dashboard.php" class="active">
                <i class="fas fa-dashboard"></i>
                Dashboard
            </a>
            <a href="add_game.php">
                <i class="fas fa-plus"></i>
                Add Game
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-gamepad"></i>
                <h3><?= $total_games ?></h3>
                <p>Total Games</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?= $total_users ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <h3><?= rand(150, 500) ?></h3>
                <p>Total Sales</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>$<?= number_format(rand(5000, 15000), 0) ?></h3>
                <p>Revenue</p>
            </div>
        </div>
        
        <!-- Games Management -->
        <div class="games-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-gamepad"></i>
                    Game Management
                </h2>
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchGames" placeholder="Search games...">
                    </div>
                    <a href="add_game.php" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Add New Game
                    </a>
                </div>
            </div>
            
            <?php if($result->num_rows > 0): ?>
                <div class="games-grid" id="gamesGrid">
                    <?php while($game = $result->fetch_assoc()): ?>
                        <div class="game-card" data-title="<?= strtolower(htmlspecialchars($game['title'])) ?>">
                            <div class="game-image">
                                <?php if(!empty($game['image']) && file_exists('../' . $game['image'])): ?>
                                    <img src="../<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['title']) ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                        <span>No Image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="game-info">
                                <div class="game-title"><?= htmlspecialchars($game['title']) ?></div>
                                <div class="game-description"><?= htmlspecialchars($game['description']) ?></div>
                                <div class="game-price">$<?= number_format($game['price'], 2) ?></div>
                                <div class="game-actions">
                                    <a href="edit_game.php?id=<?= $game['id'] ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <a href="delete_game.php?id=<?= $game['id'] ?>" 
                                       class="action-btn delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this game? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-gamepad"></i>
                    <h3>No Games Found</h3>
                    <p>Start building your game catalog by adding your first game.</p>
                    <br>
                    <a href="add_game.php" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Add Your First Game
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchGames');
        const gamesGrid = document.getElementById('gamesGrid');
        
        if (searchInput && gamesGrid) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const gameCards = gamesGrid.querySelectorAll('.game-card');
                let visibleCount = 0;
                
                gameCards.forEach(card => {
                    const title = card.dataset.title;
                    if (title.includes(searchTerm)) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show empty state if no games match
                if (visibleCount === 0 && searchTerm.length > 0) {
                    if (!document.getElementById('searchEmptyState')) {
                        const emptyState = document.createElement('div');
                        emptyState.id = 'searchEmptyState';
                        emptyState.className = 'empty-state';
                        emptyState.innerHTML = `
                            <i class="fas fa-search"></i>
                            <h3>No games found</h3>
                            <p>Try adjusting your search terms.</p>
                        `;
                        gamesGrid.appendChild(emptyState);
                    }
                } else {
                    const existingEmptyState = document.getElementById('searchEmptyState');
                    if (existingEmptyState) {
                        existingEmptyState.remove();
                    }
                }
            });
        }
        
        // Animate stats on load
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-card h3');
            statNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent.replace(/[^0-9]/g, ''));
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    stat.textContent = stat.textContent.includes('
                ) ? 
                        '
                 + Math.floor(currentValue).toLocaleString() : 
                        Math.floor(currentValue);
                }, 30);
            });
        });
        
        // Add hover effects to game cards
        document.querySelectorAll('.game-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>