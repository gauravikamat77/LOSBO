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

    // ✅ GET ALL FIELDS
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $gender = $_POST['gender'] ?? '';
    $language = trim($_POST['language'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    // ✅ VALIDATION
    if (!$name || !$email) {
        header("Location: profile.php?error=1");
        exit();
    }

    if (!preg_match('/^[0-9]{6}$/', $pincode) && !empty($pincode)) {
        die("Invalid pincode.");
    }

    if ($phone && !preg_match('/^[0-9]{10}$/', $phone)) {
        die("Invalid phone number.");
    }

    // ✅ IMAGE UPLOAD
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

    // ✅ SQL UPDATE
    if ($profile_image) {

        $sql = "UPDATE users SET 
            name=?, email=?, phone=?, profile_image=?,
            gender=?, language=?, dob=?, city=?, state=?, pincode=?
            WHERE id=?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssssssssi",
            $name, $email, $phone, $profile_image,
            $gender, $language, $dob, $city, $state, $pincode,
            $user_id
        );

    } else {

        $sql = "UPDATE users SET 
            name=?, email=?, phone=?,
            gender=?, language=?, dob=?, city=?, state=?, pincode=?
            WHERE id=?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssssssi",
            $name, $email, $phone,
            $gender, $language, $dob, $city, $state, $pincode,
            $user_id
        );
    }

    // ✅ EXECUTE
    if ($stmt->execute()) {
        header("Location: profile.php?success=1");
        exit();
    } else {
        die("Update failed: " . $stmt->error);
    }
}

// ✅ FETCH USER DATA (for form display)
$sql = "SELECT name, email, phone, profile_image, gender, language, dob, city, state, pincode 
        FROM users WHERE id=? AND role='customer'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fallback image
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

<div class="glass-card profile-main-card" style="max-width: 550px; width: 95%; padding: 40px;">

<h2 style="text-align:center;">Edit Profile</h2>

<form method="POST" enctype="multipart/form-data" class="form-group">

<!-- PROFILE IMAGE -->
<div style="text-align:center; margin-bottom:20px;">
<img src="../uploads/profiles/<?php echo $profile_img; ?>" 
     style="width:100px; height:100px; border-radius:50%; object-fit:cover;">
<br><br>
<input type="file" name="profile_image">
</div>

<!-- BASIC -->
<input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Full Name" required>
<br>

<input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
<br>

<input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="Phone">
<br>

<!-- NEW FIELDS -->
<select name="gender">
<option value="">Gender</option>
<option value="Male" <?php if($user['gender']=="Male") echo "selected"; ?>>Male</option>
<option value="Female" <?php if($user['gender']=="Female") echo "selected"; ?>>Female</option>
<option value="Other" <?php if($user['gender']=="Other") echo "selected"; ?>>Other</option>
</select>
<br>

<input type="text" name="language" value="<?php echo htmlspecialchars($user['language']); ?>" placeholder="Language">
<br>

<input type="date" name="dob" value="<?php echo $user['dob']; ?>">
<br>

<input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" placeholder="City">
<br>

<input type="text" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" placeholder="State">
<br>

<input type="text" name="pincode" value="<?php echo htmlspecialchars($user['pincode']); ?>" placeholder="Pincode">
<br><br>

<button class="btn" style="width:100%;">Update Profile</button>

</form>

</div>
</div>

</body>
</html>
