<?php
include '../config.php';
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

$id = (int)$_GET['id'];
$error = '';
$success = '';

// Get current game data
$game_result = $conn->query("SELECT * FROM games WHERE id=$id");
if($game_result->num_rows == 0) {
    header("Location: dashboard.php");
    exit;
}
$game = $game_result->fetch_assoc();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image_path = $game['image']; // Keep existing image by default
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        
        // Validate file type
        if(!in_array($file_type, $allowed_types)) {
            $error = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.";
        }
        // Validate file size
        elseif($file_size > $max_size) {
            $error = "File too large. Maximum size is 5MB.";
        }
        else {
            // Create uploads directory if it doesn't exist
            $upload_dir = '../uploads/games/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = uniqid('game_') . '.' . $file_extension;
            $upload_path = $upload_dir . $unique_name;
            
            // Move uploaded file
            if(move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old image if it exists
                if(!empty($game['image']) && file_exists('../' . $game['image'])) {
                    unlink('../' . $game['image']);
                }
                $image_path = 'uploads/games/' . $unique_name;
            } else {
                $error = "Failed to upload image. Please try again.";
            }
        }
    }
    
    // Handle image removal
    if(isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if(!empty($game['image']) && file_exists('../' . $game['image'])) {
            unlink('../' . $game['image']);
        }
        $image_path = '';
    }
    
    // Update game if no errors
    if(empty($error)) {
        $stmt = $conn->prepare("UPDATE games SET title=?, description=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("ssdsi", $title, $desc, $price, $image_path, $id);
        
        if($stmt->execute()) {
            $success = "Game updated successfully!";
            // Refresh game data
            $game['title'] = $title;
            $game['description'] = $desc;
            $game['price'] = $price;
            $game['image'] = $image_path;
        } else {
            $error = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game - GameVault Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 800px;
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
            margin-bottom: 2rem;
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
        
        .admin-nav a:hover {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
        }
        
        .current-image {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .current-image img {
            max-width: 300px;
            max-height: 200px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
        }
        
        .image-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .remove-current-btn {
            padding: 0.5rem 1rem;
            background: var(--error);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .remove-current-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--gray-light);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            font-family: inherit;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        
        .image-upload {
            position: relative;
            border: 2px dashed var(--gray-light);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .image-upload:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }
        
        .image-upload.dragover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            transform: scale(1.02);
        }
        
        .image-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .upload-content {
            pointer-events: none;
        }
        
        .upload-content i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1rem;
            display: block;
        }
        
        .upload-content h3 {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .upload-content p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .image-preview {
            margin-top: 1rem;
            display: none;
        }
        
        .image-preview img {
            max-width: 300px;
            max-height: 200px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        
        .remove-image {
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--error);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .remove-image:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .submit-btn {
            flex: 1;
            min-width: 200px;
            padding: 1.25rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .cancel-btn {
            padding: 1.25rem 2rem;
            background: var(--gray);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .cancel-btn:hover {
            background: var(--surface-dark);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 0 1rem;
            }
            
            .form-container {
                padding: 2rem;
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
            
            .form-actions {
                flex-direction: column;
            }
            
            .image-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>
                <i class="fas fa-edit"></i>
                Edit Game
            </h1>
            <p>Update game information and manage images</p>
        </div>
        
        <div class="admin-nav">
            <a href="dashboard.php">
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
        
        <?php if($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <!-- Current Image Display -->
            <?php if(!empty($game['image']) && file_exists('../' . $game['image'])): ?>
                <div class="current-image">
                    <h3>Current Image</h3>
                    <img src="../<?= htmlspecialchars($game['image']) ?>" alt="Current game image">
                    <div class="image-actions">
                        <button type="button" class="remove-current-btn" onclick="removeCurrentImage()">
                            <i class="fas fa-trash"></i>
                            Remove Current Image
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" id="gameForm">
                <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                
                <div class="form-group">
                    <label for="title">
                        <i class="fas fa-gamepad"></i>
                        Game Title
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required 
                           placeholder="Enter game title"
                           value="<?= htmlspecialchars($game['title']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-align-left"></i>
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              required 
                              placeholder="Enter game description"><?= htmlspecialchars($game['description']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">
                        <i class="fas fa-dollar-sign"></i>
                        Price
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           step="0.01" 
                           min="0" 
                           required 
                           placeholder="0.00"
                           value="<?= htmlspecialchars($game['price']) ?>">
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-image"></i>
                        <?= !empty($game['image']) ? 'Replace Image' : 'Add Image' ?>
                    </label>
                    <div class="image-upload" id="imageUpload">
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3><?= !empty($game['image']) ? 'Drop new image here or click to browse' : 'Drop image here or click to browse' ?></h3>
                            <p>Supports: JPG, PNG, GIF, WebP (Max: 5MB)</p>
                        </div>
                    </div>
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="Preview">
                        <br>
                        <button type="button" class="remove-image" id="removeImage">
                            <i class="fas fa-trash"></i>
                            Remove New Image
                        </button>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Update Game
                    </button>
                    <a href="dashboard.php" class="cancel-btn">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const imageUpload = document.getElementById('imageUpload');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const removeImage = document.getElementById('removeImage');
        const submitBtn = document.getElementById('submitBtn');
        const gameForm = document.getElementById('gameForm');
        const removeImageFlag = document.getElementById('removeImageFlag');

        // Handle drag and drop
        imageUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUpload.classList.add('dragover');
        });

        imageUpload.addEventListener('dragleave', () => {
            imageUpload.classList.remove('dragover');
        });

        imageUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUpload.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageUpload(files[0]);
            }
        });

        // Handle file input change
        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleImageUpload(e.target.files[0]);
            }
        });

        // Handle image upload
        function handleImageUpload(file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File too large. Maximum size is 5MB.');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                imageUpload.style.display = 'none';
                removeImageFlag.value = '0'; // Reset remove flag
            };
            reader.readAsDataURL(file);
        }

        // Remove new image
        removeImage.addEventListener('click', () => {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            imageUpload.style.display = 'block';
            previewImg.src = '';
        });

        // Remove current image
        function removeCurrentImage() {
            if (confirm('Are you sure you want to remove the current image?')) {
                removeImageFlag.value = '1';
                document.querySelector('.current-image').style.display = 'none';
                
                // Update upload text
                const uploadContent = document.querySelector('.upload-content h3');
                uploadContent.textContent = 'Drop image here or click to browse';
                
                // Update label
                const label = document.querySelector('label[for="image"]');
                label.innerHTML = '<i class="fas fa-image"></i> Add Image';
            }
        }

        // Form submission
        gameForm.addEventListener('submit', function(e) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Game...';
            submitBtn.disabled = true;
            
            // Re-enable if there's an error (page doesn't redirect)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Input animations
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Confirmation for leaving with unsaved changes
        let formChanged = false;
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', () => {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        gameForm.addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
</body>
</html>