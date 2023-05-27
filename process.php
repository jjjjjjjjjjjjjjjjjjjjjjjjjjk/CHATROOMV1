<?php
// Location of the text file where messages are stored
$filename = 'messages.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A message has been sent
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Format the message
    $formattedMessage = $username . ': ' . $message . PHP_EOL;

    // Append the message to the text file
    file_put_contents($filename, $formattedMessage, FILE_APPEND);
} else {
    // No message has been sent; return the existing messages
    $messages = file($filename, FILE_IGNORE_NEW_LINES);

    // Parse the messages into a list of arrays
    $parsedMessages = array();
    foreach ($messages as $message) {
        list($username, $text) = explode(': ', $message, 2);

        $parsedMessages[] = array(
            'username' => $username,
            'text' => $text
        );
    }

    // Return the parsed messages as a JSON-encoded string
    echo json_encode($parsedMessages);
}
?>
