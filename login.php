LOGIN PAGE <?php
session_start();

/* DATABASE CONNECTION */
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hostel_inventory";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database not connected!");
}

$error = "";




/* IF ALREADY LOGGED IN → GO TO DASHBOARD */
if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit();
}

/* LOGIN PROCESS */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)){
        $error = "Please fill all fields!";
    } else {

        /* PREPARED STATEMENT (SECURE) */
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            /* If you are NOT using password_hash yet */
            if($password === $user['password']) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid Username or Password!";
            }

        } else {
            $error = "Invalid Username or Password!";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Department Stock Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
        }
        .login-card {
            border-radius: 20px;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">

<div class="card shadow-lg p-4 login-card" style="width: 400px;">
    <h3 class="text-center mb-4">Department Stock Control</h3>

    <?php if($error != ""): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Login
        </button>
    </form>
</div>

</body>
</html>