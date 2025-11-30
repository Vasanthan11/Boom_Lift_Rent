<?php
// submit_form.php

// OPTIONAL: turn off notices in production
// error_reporting(E_ALL & ~E_NOTICE);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Direct access without POST – just block politely
    http_response_code(405);
    echo "Method not allowed.";
    exit;
}

// Helper function: trim + basic sanitise
function clean_input($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// Get and clean fields
$fullname  = clean_input($_POST['fullname'] ?? '');
$email     = clean_input($_POST['email'] ?? '');
$phone     = clean_input($_POST['phone'] ?? '');
$location  = clean_input($_POST['location'] ?? '');
$equipment = clean_input($_POST['equipment'] ?? '');
$message   = clean_input($_POST['message'] ?? '');

// Basic validation
$errors = [];

if ($fullname === '') {
    $errors[] = "Full name is required.";
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
}

if ($phone === '') {
    $errors[] = "Contact number is required.";
}

if ($location === '') {
    $errors[] = "Area / location is required.";
}

if ($equipment === '') {
    $errors[] = "Please select the equipment.";
}

// Email setup
$toEmail = "info@boomliftrentalsdelhi.com"; // <-- change if needed
$subject = "New Enquiry from BoomLift Rentals Website";

$bodyLines = [
    "You have received a new enquiry from the website.",
    "",
    "Full Name:   " . $fullname,
    "Email:       " . $email,
    "Phone:       " . $phone,
    "Location:    " . $location,
    "Equipment:   " . $equipment,
    "",
    "Message / Request:",
    $message !== '' ? $message : "(No additional message provided.)",
    "",
    "Sent on: " . date('Y-m-d H:i:s')
];

$body = implode("\n", $bodyLines);

// Basic header injection protection for email header fields
$safeEmail   = str_replace(["\r", "\n"], '', $email);
$safeName    = str_replace(["\r", "\n"], '', $fullname);

$headers   = "From: BoomLift Rentals <no-reply@yourdomain.com>\r\n";
$headers  .= "Reply-To: {$safeName} <{$safeEmail}>\r\n";
$headers  .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Try to send email only if no validation errors
$mailSent = false;

if (empty($errors)) {
    $mailSent = @mail($toEmail, $subject, $body, $headers);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enquiry Response – BoomLift Rentals</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Roboto -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: "Roboto", sans-serif;
      background: #e0d9d9;
      color: #111111;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .response-wrapper {
      max-width: 600px;
      width: 100%;
      background: #ffffff;
      border-radius: 14px;
      border: 2px solid #2f5755;
      padding: 2rem 2.2rem;
    }
    .response-title {
      text-transform: uppercase;
      letter-spacing: 0.12em;
      font-size: 1rem;
      color: #432323;
      margin-bottom: 0.75rem;
    }
    .response-message {
      font-size: 0.95rem;
      margin-bottom: 1rem;
      color: #333333;
    }
    .response-errors {
      background: #432323;
      color: #ffffff;
      border-radius: 10px;
      padding: 0.9rem 1rem;
      font-size: 0.9rem;
      margin-bottom: 1.2rem;
    }
    .response-errors ul {
      margin-left: 1.1rem;
      margin-top: 0.4rem;
    }
    .response-note {
      font-size: 0.82rem;
      color: #555555;
      margin-top: 0.6rem;
    }
    .btn-row {
      margin-top: 1.4rem;
      display: flex;
      gap: 0.75rem;
      flex-wrap: wrap;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.7rem 1.4rem;
      border-radius: 999px;
      border: none;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s ease, color 0.2s ease;
    }
    .btn-primary {
      background: #2f5755;
      color: #ffffff;
    }
    .btn-primary:hover {
      background: #ffffff;
      color: #2f5755;
    }
    .btn-ghost {
      background: #e0d9d9;
      color: #432323;
    }
    .btn-ghost:hover {
      background: #432323;
      color: #ffffff;
    }
  </style>
</head>
<body>
  <div class="response-wrapper">
    <?php if (!empty($errors)): ?>
      <h1 class="response-title">There was a problem with your enquiry</h1>
      <p class="response-message">
        Please review the points below, go back to the form and correct the highlighted items.
      </p>
      <div class="response-errors">
        <strong>Missing / incorrect details:</strong>
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?php echo $err; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="btn-row">
        <a href="javascript:history.back()" class="btn btn-primary">
          Go back to form
        </a>
        <a href="index.html" class="btn btn-ghost">
          Go to home
        </a>
      </div>
    <?php else: ?>
      <?php if ($mailSent): ?>
        <h1 class="response-title">Thank you for your enquiry</h1>
        <p class="response-message">
          We’ve received your details and will get back to you as soon as possible with suitable
          boom lift, scissor lift or man lift options and a customised quote.
        </p>
        <p class="response-note">
          If you need to speak urgently, you can also call us on <strong>+91 99999 99999</strong>.
        </p>
        <div class="btn-row">
          <a href="index.html" class="btn btn-primary">
            Return to home
          </a>
        </div>
      <?php else: ?>
        <h1 class="response-title">We couldn’t send your enquiry</h1>
        <p class="response-message">
          Your form was valid, but we were unable to send the email from the server right now.
          This is usually a temporary issue or a mail configuration problem.
        </p>
        <p class="response-note">
          Please try again in a few minutes or send your requirement directly to
          <strong>info@boomliftrentalsdelhi.com</strong>.
        </p>
        <div class="btn-row">
          <a href="javascript:history.back()" class="btn btn-primary">
            Try again
          </a>
          <a href="mailto:info@boomliftrentalsdelhi.com" class="btn btn-ghost">
            Email us directly
          </a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>
</html>
