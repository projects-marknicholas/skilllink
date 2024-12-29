<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve POST data
  $pid = $_POST['pid'];
  $sname = $_POST['sname'];
  $status = $_POST['status'];

  // Find the seeker ID (sid) and email based on sname
  $sidQuery = "SELECT id, email FROM seeker WHERE name = ?";
  $stmt = $conn->prepare($sidQuery);
  $stmt->bind_param('s', $sname);
  $stmt->execute();
  $sidResult = $stmt->get_result();

  if ($sidResult->num_rows > 0) {
    $seekerData = $sidResult->fetch_assoc();
    $sid = $seekerData['id'];
    $email = $seekerData['email'];

    // Update the jobsapplied table
    $updateQuery = "UPDATE jobsapplied SET status = ? WHERE pid = ? AND sid = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sii', $status, $pid, $sid);

    if ($stmt->execute()) {
      // Send email notification
      $emailStatus = sendEmailNotification($email, $sname, $status);

      echo json_encode([
        'status' => 'success',
        'email' => $email,
        'message' => 'Status updated successfully.',
        'email_status' => $emailStatus ? 'Email sent successfully.' : 'Failed to send email.',
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update status.',
      ]);
    }
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Applicant not found.',
    ]);
  }

  $stmt->close();
  $conn->close();
}

/**
 * Sends an email notification using the Brevo API
 */
function sendEmailNotification($email, $name, $status) {
  $apiKey = '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';
  $url = 'https://api.brevo.com/v3/smtp/email';

  $data = [
    'sender' => [
      'name' => 'Skill Link',
      // 'email' => 'skilllink69@gmail.com',
      'email' => 'razonmarknicholas.cdlb@gmail.com',
    ],
    'to' => [
      [
        'email' => $email,
        'name' => $name,
      ],
    ],
    'subject' => 'Application Status Update',
    'htmlContent' => '<p>Dear ' . htmlspecialchars($name) . ',</p>
                      <p>Your application status has been updated to: <strong>' . htmlspecialchars($status) . '</strong>.</p>
                      <p>Thank you for applying!</p>',
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'api-key: ' . $apiKey,
  ]);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  return $httpCode === 201; 
}
