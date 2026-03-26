<?php
session_start();
include("../config/database.php");

// Ensure user is logged in
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    die("Please log in first.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Handle profile image upload
    $profile_image = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_ext = ['jpg','jpeg','png','gif'];
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid('profile_') . '.' . $file_ext;
            $upload_dir = '../uploads/profiles/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                $profile_image = $new_filename;
            }
        } else {
            die("Invalid image type. Allowed: jpg, jpeg, png, gif.");
        }
    }

    // Build SQL depending on whether profile image was uploaded
    if ($profile_image) {
        $sql = "UPDATE users SET name=?, email=?, phone=?, profile_image=? WHERE id=?";
    } else {
        $sql = "UPDATE users SET name=?, email=?, phone=? WHERE id=?";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    if ($profile_image) {
        $stmt->bind_param("ssssi", $name, $email, $phone, $profile_image, $user_id);
    } else {
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
    }

    // Execute
    if ($stmt->execute()) {
        // Success: redirect to profile page
        header("Location: profile.php?update=success");
        exit();
    } else {
        die("Update failed: " . $stmt->error);
    }
}

// Fetch current user data to populate the form
$sql = "SELECT name, email, phone, profile_image FROM users WHERE id=? AND role='customer'";
$stmt = $conn->prepare($sql);
if (!$stmt) { die("Prepare failed: " . $conn->error); }
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_img = !empty($user['profile_image']) ? $user['profile_image'] : 'default.png';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include("navbar.php"); ?>

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 60px;">
    <div class="glass-card profile-main-card">
        <h2>Edit Profile</h2>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="form-group">
            <div class="image-container" style="margin-bottom: 15px;">
                <img src="../uploads/profiles/<?php echo $profile_img; ?>" alt="Profile Picture" class="profile-pic-large">
            </div>
            <label>Change Profile Picture:</label>
            <input type="file" name="profile_image" accept="image/*">

            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

            <button type="submit" class="btn" style="margin-top: 15px;">Update Profile</button>
        </form>
    </div>
</div>
</body>
</html>