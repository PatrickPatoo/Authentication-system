<?php

session_start();

require_once "config/database.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Check that fields are not empty

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";
        $messageType = "error";

    } else {

        // Find user by email

        $sql = "SELECT id, name, email, password
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        // Check if user exists

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();


            // Verify password

            if (password_verify($password, $user["password"])) {

                // Login successful

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];


                // Redirect to dashboard

                header("Location: dashboard.php");
                exit;

            } else {

                $message = "Incorrect email or password.";
                $messageType = "error";

            }

        } else {

            $message = "Incorrect email or password.";
            $messageType = "error";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <div class="auth-container">

        <div class="auth-box">

            <h1>Welcome Back</h1>

            <p class="subtitle">
                Login to your account
            </p>


            <?php if ($message !== ""): ?>

                <div class="<?php echo $messageType; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST"
                  action="login.php">


                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button type="submit" class="btn">
                    Login
                </button>

            </form>


            <p class="switch-page">

                Don't have an account?

                <a href="register.php">
                    Create Account
                </a>

            </p>

        </div>

    </div>

</body>

</html>