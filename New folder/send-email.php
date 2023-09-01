<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $recipient = $_POST['recipient'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $attachment = $_FILES['attachment'];

    $to = $recipient;
    $from = 'cst20026@email.com'; // Replace with your email address

    // Create a boundary for the email
    $boundary = md5(uniqid());

    // Headers
    $headers = "From: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Message
    $email_message = "--$boundary\r\n";
    $email_message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $email_message .= "Content-Transfer-Encoding: 7bit\r\n";
    $email_message .= "\r\n$message\r\n";

    // Attachment
    if (!empty($attachment) && $attachment['error'] === UPLOAD_ERR_OK) {
        $file_content = file_get_contents($attachment['tmp_name']);
        $file_name = $attachment['name'];
        $file_type = $attachment['type'];

        $email_message .= "--$boundary\r\n";
        $email_message .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $email_message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $email_message .= "Content-Transfer-Encoding: base64\r\n";
        $email_message .= "\r\n" . chunk_split(base64_encode($file_content)) . "\r\n";
    }

    $email_message .= "--$boundary--";

    // Send the email
    if (mail($to, $subject, $email_message, $headers)) {
        http_response_code(200);
    } else {
        http_response_code(500);
    }
}
?>
