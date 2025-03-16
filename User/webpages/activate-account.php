<?php
$token = isset($_GET["token"]) ? $_GET["token"] : null;
if ($token === null) {
    die("Token not found");
}

$token_hash = hash("sha256", $token);

// Include the database class with the correct path
require_once __DIR__ . "/../classes/database.php";

// Create a new instance of the Database class
$database = new Database();

// Call the connect method to establish a connection to the database
$conn = $database->connect();

$sql = "SELECT * FROM user WHERE account_activation_hash = :token_hash";

$stmt = $conn->prepare($sql);

$stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR); // Use bindValue instead of bindParam

$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user === false) {
    die("Token not found in the database222");
}

// Check if account_activation_hash matches the token
if ($user['account_activation_hash'] === $token_hash) {
    // Update the user's account activation status in the database
    $sql_update = "UPDATE user SET account_activation_hash = NULL WHERE userID = :userID";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(":userID", $user["userID"], PDO::PARAM_INT);
    $stmt_update->execute();

    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Account Activated</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    </head>
    <body>

    <h1>Account Activated</h1>

    <p>Account activated successfully. You can now <a href="../index.php">log in</a>.</p>

    </body>
    </html>

<?php
} else {
    die("Token does not match account activation hash");
}
?>