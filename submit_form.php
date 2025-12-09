<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect & sanitize inputs
    $fullname   = trim($_POST['fullname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $location   = trim($_POST['location'] ?? '');
    $equipment  = trim($_POST['equipment'] ?? '');

    // Basic validation
    if (empty($fullname) || empty($phone)) {
        echo "Full Name and Contact Number are required.";
        exit;
    }

    // Email settings
    $to = "info@boomliftrent.com";
    $subject = "New Equipment Rental Enquiry";
    
    $message = "
New Enquiry Received\n
-----------------------------\n
Full Name   : $fullname
Email       : $email
Phone       : $phone
Location    : $location
Equipment   : $equipment
-----------------------------\n
Sent from website enquiry form.
";

    $headers = "From: BoomLift Website <no-reply@boomliftrent.com>\r\n";
    $headers .= "Reply-To: " . ($email ? $email : "no-reply@boomliftrent.com") . "\r\n";

    // Send Email
    if (mail($to, $subject, $message, $headers)) {
        // Redirect to thank you page
        header("Location: thankyou.html");
        exit;
    } else {
        echo "Failed to send enquiry. Please try again later.";
    }

} else {
    echo "Invalid request.";
}
?>
