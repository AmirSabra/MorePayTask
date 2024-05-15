<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "db_config.php";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["selected_file"]) && $_FILES["selected_file"]["error"] == 0) {
        $file_name = $_FILES["selected_file"]["name"];
        $file_type = $_FILES["selected_file"]["type"];
        $file_size = $_FILES["selected_file"]["size"];
    
        // Verify file size - 5MB maximum
        $max_size = 5 * 1024 * 1024;
        if ($file_size > $max_size) die("Error: File size is larger than the allowed limit.");
    
            // Check whether file exists before uploading it
            if (file_exists("uploads/" . $file_name)) {
                echo $file_name . " already exists.";
            } else {

                if (!file_exists('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                // Prepare an insert statement
                $sql = "INSERT INTO uploads (user_id, directory, file_name) VALUES (?, ?, ?)";
                
                if ($stmt = $mysqli->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters to prevent SQL Injection
                    $stmt->bind_param("sss", $param_user_id, $param_directory, $param_file_name);
                    
                    // Set parameters
                    $param_user_id = $_SESSION["id"];
                    $param_directory = "uploads/";
                    $param_file_name = $_FILES["selected_file"]["name"];
                    
                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Redirect to login page
                        move_uploaded_file($_FILES["selected_file"]["tmp_name"], "uploads/" . $file_name);

                        echo "Your File was Uploaded Successfully.";
                        echo "File Name: " . $_FILES["selected_file"]["name"] . "<br>";
                        echo "File Type: " . $_FILES["selected_file"]["type"] . "<br>";
                        echo "File Size: " . ($_FILES["selected_file"]["size"] / 1024) . " KB<br>";
                        echo "Stored in: " . $_FILES["selected_file"]["tmp_name"];
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    $stmt->close();
                }
            } 
    } else {
        echo "Error: " . $_FILES["selected_file"]["error"];
    }
}
?>
 
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Welcome</title>
        <!-- CSS Imports -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.css">
        <!-- JS Imports -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap4.js"></script>
        <style>
            body{ font: 14px sans-serif; }
            .wrapper{ width: 752px; padding: 20px; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <h2>Hi <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, please start by Uploading a File below:</h2><br />

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" class="form-control-file" name="selected_file">
                    <p class="text-danger">5MB maximum per File Size</p>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Upload">
                </div>
            </form>
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>        
                <?php
                    $sql = "SELECT * FROM uploads WHERE user_id = ?";
                        
                    if ($stmt = $mysqli->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters to prevent SQL Injection
                    $stmt->bind_param("s", $param_user_id);

                    $param_user_id = $_SESSION["id"];
                            
                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        while($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['directory'] . $row['file_name']; ?></td>
                        </tr>
                <?php
                        }
                    }
                    // Close statement
                    $stmt->close();
                    }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>File</th>
                    </tr>
                </tfoot>
            </table>
        </div>        

    <script>
        new DataTable('#example');
    </script>

        <p>
            <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        </p>
    </body>
</html>