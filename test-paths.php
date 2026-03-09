<?php
require_once __DIR__ . "/layouts/config.php";
require_once __DIR__ . "/admin/includes/db.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Path Testing - Athletes Gym</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .path-info {
            background: #fff;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <h1>Path Testing - Athletes Gym</h1>

    <div class="section">
        <h2>1. Server Configuration</h2>
        <div class="path-info">
            <strong>BASE_URL:</strong> <?= BASE_URL ?><br>
            <strong>Document Root:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?><br>
            <strong>Script Filename:</strong> <?= $_SERVER['SCRIPT_FILENAME'] ?><br>
            <strong>HTTP Host:</strong> <?= $_SERVER['HTTP_HOST'] ?><br>
            <strong>Request URI:</strong> <?= $_SERVER['REQUEST_URI'] ?><br>
        </div>
    </div>

    <div class="section">
        <h2>2. Directory Structure Check</h2>
        <?php
        $directories = [
            'uploads' => __DIR__ . '/uploads',
            'uploads/categories' => __DIR__ . '/uploads/categories',
            'assets/css' => __DIR__ . '/assets/css',
            'assets/js' => __DIR__ . '/assets/vendors/js'
        ];

        echo '<table>';
        echo '<tr><th>Directory</th><th>Status</th><th>Permissions</th></tr>';
        foreach ($directories as $name => $path) {
            $exists = is_dir($path);
            $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
            $status = $exists ? '<span class="success">✓ EXISTS</span>' : '<span class="error">✗ NOT FOUND</span>';
            echo "<tr><td>$name</td><td>$status</td><td>$perms</td></tr>";
        }
        echo '</table>';
        ?>
    </div>

    <div class="section">
        <h2>3. Sample Product Images Test</h2>
        <?php
        // Get some product images from database
        $stmt = $pdo->query("SELECT image_path FROM product_images LIMIT 5");
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($images) {
            echo '<table>';
            echo '<tr><th>Database Path</th><th>Full URL</th><th>File Exists?</th><th>Preview</th></tr>';

            foreach ($images as $img) {
                $fullPath = __DIR__ . '/uploads/' . $img;
                $fullUrl = BASE_URL . 'uploads/' . $img;
                $exists = file_exists($fullPath);
                $status = $exists ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>';

                echo '<tr>';
                echo '<td>' . htmlspecialchars($img) . '</td>';
                echo '<td><a href="' . $fullUrl . '" target="_blank">' . htmlspecialchars($fullUrl) . '</a></td>';
                echo '<td>' . $status . '</td>';
                echo '<td>';
                if ($exists) {
                    echo '<img src="' . $fullUrl . '" alt="product" onerror="this.src=\'\'; this.alt=\'Failed to load\';">';
                } else {
                    echo '<span class="error">File not found on server</span>';
                }
                echo '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p class="error">No product images found in database</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Sample Category Images Test</h2>
        <?php
        $stmt = $pdo->query("SELECT id, name, image FROM categories LIMIT 5");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($categories) {
            echo '<table>';
            echo '<tr><th>Category</th><th>Database Path</th><th>Full URL</th><th>File Exists?</th><th>Preview</th></tr>';

            foreach ($categories as $cat) {
                $imagePath = $cat['image'];

                // Handle both old and new format
                if ($imagePath && strpos($imagePath, 'uploads/') !== 0) {
                    $imagePath = 'uploads/categories/' . $imagePath;
                }

                $fullPath = __DIR__ . '/' . $imagePath;
                $fullUrl = BASE_URL . $imagePath;
                $exists = $imagePath ? file_exists($fullPath) : false;
                $status = $exists ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>';

                echo '<tr>';
                echo '<td>' . htmlspecialchars($cat['name']) . '</td>';
                echo '<td>' . htmlspecialchars($cat['image']) . '</td>';
                echo '<td><a href="' . $fullUrl . '" target="_blank">' . htmlspecialchars($fullUrl) . '</a></td>';
                echo '<td>' . $status . '</td>';
                echo '<td>';
                if ($exists) {
                    echo '<img src="' . $fullUrl . '" alt="category" onerror="this.src=\'\'; this.alt=\'Failed to load\';">';
                } else {
                    echo '<span class="error">File not found on server</span>';
                }
                echo '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p class="error">No categories found in database</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>5. CSS/JS Files Test</h2>
        <?php
        $assets = [
            'CSS - Bootstrap' => 'assets/vendors/bootstrap/bootstrap.min.css',
            'CSS - App' => 'assets/css/app.css',
            'JS - jQuery' => 'assets/vendors/js/jquery.min.js',
            'JS - Custom' => 'assets/vendors/js/custom.js'
        ];

        echo '<table>';
        echo '<tr><th>Asset</th><th>Full URL</th><th>File Exists?</th></tr>';

        foreach ($assets as $name => $path) {
            $fullPath = __DIR__ . '/' . $path;
            $fullUrl = BASE_URL . $path;
            $exists = file_exists($fullPath);
            $status = $exists ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>';

            echo '<tr>';
            echo '<td>' . htmlspecialchars($name) . '</td>';
            echo '<td><a href="' . $fullUrl . '" target="_blank">' . htmlspecialchars($fullUrl) . '</a></td>';
            echo '<td>' . $status . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        ?>
    </div>

    <div class="section">
        <h2>6. Upload Directory Contents (First 10 files)</h2>
        <?php
        $uploadDir = __DIR__ . '/uploads/';
        if (is_dir($uploadDir)) {
            $files = array_slice(scandir($uploadDir), 2, 10); // Skip . and ..
            echo '<ul>';
            foreach ($files as $file) {
                $size = filesize($uploadDir . $file);
                echo '<li>' . htmlspecialchars($file) . ' (' . number_format($size) . ' bytes)</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="error">Uploads directory not found!</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>7. .htaccess Check</h2>
        <?php
        $htaccessFiles = [
            'Root .htaccess' => __DIR__ . '/.htaccess',
            'Uploads .htaccess' => __DIR__ . '/uploads/.htaccess'
        ];

        echo '<table>';
        echo '<tr><th>File</th><th>Exists?</th><th>Content Preview</th></tr>';

        foreach ($htaccessFiles as $name => $path) {
            $exists = file_exists($path);
            $status = $exists ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>';
            $content = $exists ? htmlspecialchars(substr(file_get_contents($path), 0, 200)) . '...' : 'N/A';

            echo '<tr>';
            echo '<td>' . htmlspecialchars($name) . '</td>';
            echo '<td>' . $status . '</td>';
            echo '<td><pre style="font-size: 10px;">' . $content . '</pre></td>';
            echo '</tr>';
        }

        echo '</table>';
        ?>
    </div>

    <div class="section">
        <h2>Instructions</h2>
        <ol>
            <li>Check if BASE_URL is correct for your live server</li>
            <li>Verify all directories exist on the live server</li>
            <li>Check if image files exist on the server (File Exists column should show ✓ YES)</li>
            <li>Try clicking the Full URL links to see if you can access the files directly</li>
            <li>Check file permissions (should be 755 for directories, 644 for files)</li>
        </ol>
        <p><strong>Common Issues:</strong></p>
        <ul>
            <li>If images show "✗ NO" - The uploads folder was not deployed to live server</li>
            <li>If BASE_URL is wrong - Update layouts/config.php</li>
            <li>If you get 403 errors - Check file/folder permissions</li>
            <li>If you get 404 errors - Files are missing from server</li>
        </ul>
    </div>

    <div style="margin-top: 40px; padding: 20px; background: #fff3cd; border-radius: 8px;">
        <strong>Next Steps:</strong>
        <p>Upload this test-paths.php file to your live server and access it at: <code>https://yourdomain.com/test-paths.php</code></p>
        <p>This will show you exactly what's wrong with the paths and files on your live server.</p>
    </div>
</body>
</html>
