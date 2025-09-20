<!-- pages/admin/notifications.php -->
<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

// Get upcoming events
$stmt = $connection->prepare("
    SELECT e.id, e.title, e.event_date, i.title as initiative_title 
    FROM events e 
    JOIN initiatives i ON e.initiative_id = i.id 
    WHERE e.event_date > NOW() 
    ORDER BY e.event_date ASC 
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
$upcoming_events = $result->fetch_all(MYSQLI_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $event_id = (int)$_POST['event_id'];
    $message_content = sanitize_input($_POST['message']);
    
    // Get volunteers for this event
    $stmt = $connection->prepare("
        SELECT u.email, u.name 
        FROM volunteer_signups vs 
        JOIN users u ON vs.user_id = u.id 
        WHERE vs.event_id = ? AND vs.status = 'confirmed'
    ");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $volunteers = $result->fetch_all(MYSQLI_ASSOC);
    
    $sent_count = 0;
    foreach($volunteers as $volunteer) {
        $subject = "Event Reminder: " . $message_content;
        $body = "
        <html>
        <body>
            <h2>Event Reminder</h2>
            <p>Dear {$volunteer['name']},</p>
            <p>This is a reminder about the upcoming event:</p>
            <p><strong>{$message_content}</strong></p>
            <p>We look forward to seeing you there!</p>
            <p>Best regards,<br>GreenEarth Team</p>
        </body>
        </html>
        ";
        
        if(send_email($volunteer['email'], $subject, $body)) {
            $sent_count++;
        }
    }
    
    $message = "Notification sent to {$sent_count} volunteers!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - GreenEarth Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'components/header.php'; ?>
    
    <div class="flex">
        <?php include 'components/sidebar.php'; ?>
        
        <main class="flex-1 pb-8">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">Send Notifications</h1>
                    
                    <?php if($message): ?>
                    <div class="mt-4 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    <?php echo $message; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                    <div class="mt-4 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    <?php echo $error; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-6 bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900">Send Event Notification</h2>
                        <form method="POST" class="mt-4">
                            <div>
                                <label for="event_id" class="block text-sm font-medium text-gray-700">Select Event</label>
                                <select id="event_id" name="event_id" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">Choose an event</option>
                                    <?php foreach($upcoming_events as $event): ?>
                                    <option value="<?php echo $event['id']; ?>">
                                        <?php echo $event['title']; ?> - <?php echo $event['initiative_title']; ?> 
                                        (<?php echo date('M j, Y', strtotime($event['event_date'])); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mt-4">
                                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea id="message" name="message" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Enter your notification message..."></textarea>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" name="send_notification" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                    </svg>
                                    Send Notification
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mt-8 bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900">Upcoming Events</h2>
                        <div class="mt-4">
                            <ul class="divide-y divide-gray-200">
                                <?php foreach($upcoming_events as $event): ?>
                                <li class="py-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?php echo $event['title']; ?></p>
                                            <p class="text-sm text-gray-500"><?php echo $event['initiative_title']; ?></p>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($event['event_date'])); ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>