<?php
$pageTitle = "Edit User";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$userId = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='error-message'>User not found.</div>";
    require_once __DIR__ . "/../includes/footer.php";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? null;
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    // Validation
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email exists for another user
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkStmt->execute([$email, $userId]);
        if ($checkStmt->fetch()) {
            $error = "Email already exists for another user.";
        } else {
            try {
                $updateStmt = $pdo->prepare("UPDATE users SET
                    name = ?, email = ?, phone = ?, gender = ?, dob = ?,
                    country = ?, city = ?, address = ?, newsletter = ?
                    WHERE id = ?");
                $updateStmt->execute([
                    $name, $email, $phone, $gender, $dob,
                    $country, $city, $address, $newsletter, $userId
                ]);

                $success = "User updated successfully!";
                // Refresh user data
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
            } catch (PDOException $e) {
                $error = "Error updating user: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="page-header">
    <h1>Edit User</h1>
    <div class="page-actions">
        <a href="list.php" class="btn btn-secondary">← Back to List</a>
        <a href="view.php?id=<?= $user['id'] ?>" class="btn btn-secondary">View Details</a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" class="admin-form">
        <div class="form-section">
            <h3>Personal Information</h3>

            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?= $user['dob'] ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Location Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?= htmlspecialchars($user['country'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>Preferences</h3>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="newsletter" value="1" <?= $user['newsletter'] ? 'checked' : '' ?>>
                    <span>Newsletter Subscription</span>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3>Account Information</h3>
            <div class="info-box">
                <p><strong>User ID:</strong> <?= $user['id'] ?></p>
                <p><strong>Registered:</strong> <?= date('M d, Y H:i', strtotime($user['created_at'])) ?></p>
                <p class="note">Password cannot be changed from admin panel. User can reset password via forgot password feature.</p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="view.php?id=<?= $user['id'] ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
