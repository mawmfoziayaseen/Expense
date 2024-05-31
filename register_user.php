<?php
include('./include/header.php');
include('./include/function.php');
include('./include/db_conn.php');

$user_id = null; // Added missing semicolon

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id']; // Removed the dollar sign within the variable name
}

if (isset($_POST['submit'])) {
    $user_pic = $_FILES['user_pic'];

    $user_name = $user_pic['name'];
    $img_temp_name = $user_pic['tmp_name'];

    // Ensure the file is an image
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $img_extension = strtolower(pathinfo($user_name, PATHINFO_EXTENSION));

    if (in_array($img_extension, $allowed_extensions)) {
        // Generate a new unique file name
        $new_img_name = round(microtime(true)) . "." . $img_extension;
        $img_path = "./images/user_image/" . $new_img_name;

        // Move the uploaded file
        if (move_uploaded_file($img_temp_name, $img_path)) {
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("UPDATE reg_users SET user_pic=? WHERE reg_id=?");
            $stmt->bind_param("si", $new_img_name, $user_id);

            if ($stmt->execute()) {
                echo "Image uploaded successfully";
            } else {
                echo "Failed to update the database";
            }
            $stmt->close();
        } else {
            echo "Failed to upload image on the server.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}
?>

<div class="container">
    <div class="row py-5">
        <h1 class="text-center py-3">Upload Image</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="col-md-6 mb-3 mx-auto">
                <input type="file" class="form-control" name="user_pic" required>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <button type="submit" class="btn btn-primary w-100" name="submit">Upload</button>
            </div>
        </form>
    </div>
</div>

<?php
include('./include/footer.php');
?>
