<?php

require_once "config/database.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];


    // 1. Check required fields

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirmPassword)
    ) {

        $message = "Please fill in all fields.";
        $messageType = "error";

    }


    // 2. Validate email

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "error";

    }


    // 3. Check password length

    elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $messageType = "error";

    }


    // 4. Confirm passwords

    elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";
        $messageType = "error";

    }


    else {

        // 5. Check if email already exists

        $sql = "SELECT id FROM users WHERE email = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";
            $messageType = "error";

        } else {

            // 6. Hash password

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // 7. Insert user

            $sql = "INSERT INTO users (name, email, password)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashedPassword
            );
if ($stmt->execute()) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Successful</title>
        <link rel="stylesheet" href="css/style.css">

        <meta http-equiv="refresh" content="3;url=login.php">
    </head>

    <body>

        <div class="auth-container">
            <div class="auth-box">

                <h1>Registration Successful!</h1>

                <p class="subtitle">
                    Your account has been created successfully.
                </p>

                <div class="success">
                    Registration successful! Proceeding to login...
                </div>

                <p class="switch-page">
                    Please wait...
                </p>

            </div>
        </div>

    </body>
    </html>
    <?php
    exit;

} else {

    $message = "Something went wrong. Please try again.";
    $messageType = "error";

}
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

    <title>Create Account</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <div class="auth-container">

        <div class="auth-box">

            <h1>Create Account</h1>

            <p class="subtitle">
                Register a new account
            </p>


            <?php if ($message !== ""): ?>

                <div class="<?php echo $messageType; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST"
                  action="register.php">

                <div class="form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your full name"
                        required
                    >

                </div>


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
                        placeholder="Create a password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                    >

                </div>


                <button type="submit" class="btn">
                    Create Account
                </button>

            </form>


            <p class="switch-page">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </p>

        </div>

    </div>

</body>

</html>