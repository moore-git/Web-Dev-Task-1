<?php

# Add your login logic here.

// This is terrible and should not find its way into production
$dbUser = 'root';
$dbPass = 'secret';
$dbHost = 'localhost';

// Connect to the database
try {
    $conn = new PDO("mysql:host=$dbHost;dbname=db", $dbUser, $dbPass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die;
}

// Fetch the elements from post
$email = $_POST['email'];
$password = $_POST['password'];

$validEmailRegexPattern = '/(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/';

// Check that the email has valid formatting before proceeding
if (false === preg_match($validEmailRegexPattern, $email)) {
    // Show an error and take the user back to the login page
    showAlert(
        'Email address supplied is not valid',
        true
    );
}

// Prepared statements should help mitigate sql injection
// Try to fetch the matching record from the database
$fetchAccountStmt = $conn->prepare("
    SELECT 
      id,
      forename,
      surname,
      old_password,
      old_password_salt,
      password 
    FROM users
    WHERE
      email_address = :email
    LIMIT 1
");
$fetchAccountStmt->bindValue('email', $email, \PDO::PARAM_STR);
$fetchAccountStmt->execute();
// We are only expecting 1 record so try to fetch it now
$accountResult = $fetchAccountStmt->fetch(\PDO::FETCH_ASSOC);
$fetchAccountStmt->closeCursor();

// If no record was found return the user to the login page
if (false === $accountResult) {
    showAlert(
        'Sorry, no account could be found for the provided email address',
        true
    );
}

// If the user already has a new password try to log them in with it
if ( ! is_null($accountResult['password'])) {
    if (false === password_verify($password, $accountResult['password'])) {
        // If the password didn't match then display a message and the login screen
        showAlert(
            'Sorry, those details aren\'t correct',
            true
        );
    }
    else {
        // todo Actually sign the user in
        // Show a message that the user has been logged in
        showSuccess(sprintf(
            'Hello %s %s, you have been logged in successfully',
            $accountResult['forename'],
            $accountResult['surname']
        ));
    }
}
else {
    // This message is for debug purposes only and should be hidden from non-authorised users
    showInfo('Attempting to verify against old password');
    // todo Suppress debug alert for non-authorised users

    // Try to verify against the old password
    if (md5($password . $accountResult['old_password_salt']) !== $accountResult['old_password']) {
        // If the password didn't match then display a message and the login screen
        showAlert(
            'Sorry, those details aren\'t correct',
            true
        );
    }

    // Otherwise successful
    // hash and store the new password
    $storeNewPasswordStmt = $conn->prepare('
        UPDATE 
          users 
        SET 
          password = :password 
        WHERE 
          id = :id
    ');
    // Create and bind the new password hash using bcrypt
    $storeNewPasswordStmt->bindValue(
        'password',
        password_hash($password, PASSWORD_DEFAULT),
        \PDO::PARAM_STR
    );
    $storeNewPasswordStmt->bindValue('id', $accountResult['id'], \PDO::PARAM_INT);
    $storeNewPasswordStmt->execute();

    // This message is for debug purposes only and should be hidden from non-authorised users
    showInfo('New password hash has been generated and stored');
    // todo Suppress debug alert for non-authorised users

    // todo Remove the old password and salt values
    // Although the data structure isn't ready for that yet and preserving the data is handy for testing this exercise
    $removeOldPasswordStmt = $conn->prepare("
        UPDATE 
          users 
        SET 
          old_password = '', 
          old_password_salt = '' 
        WHERE 
          id = :id
    ");
    $removeOldPasswordStmt->bindValue('id', $accountResult['id'], \PDO::PARAM_INT);
    $removeOldPasswordStmt->execute();

    // log the user in
    // todo Actually sign the user in
    showSuccess(sprintf(
        'Hello %s %s, you have been logged in successfully',
        $accountResult['forename'],
        $accountResult['surname']
    ));
}

/**
 * @param string $message
 * @param bool $showLogin
 */
function showAlert($message, $showLogin = false)
{
    echo "<div class=\"alert alert-warning\" role=\"alert\">$message</div>";
    if ($showLogin !== false) {
        include('index.html');
        exit;
    }
}

/**
 * @param string $message
 * @param bool $exit
 */
function showSuccess($message, $exit = true)
{
    echo "<div class=\"alert alert-success\" role=\"alert\">$message</div>";
    if ($exit === true) {
        exit;
    }
}

/**
 * @param string $message
 */
function showInfo($message)
{
    echo "<div class=\"alert alert-info\" role=\"alert\">$message</div>";
}
