<?php

// if(isset($_GET['id'])){
//     $pid = $_GET['id'];
//     session_start();
//     if(isset($_SESSION['sid'])){
//         include 'connect.php';
//         $sid = $_SESSION['sid'];
        
//         $sql = "select * from jobsapplied where pid='$pid' and sid='$sid';";
//         $result=$conn->query($sql);
//         $count=$result->num_rows;
//             if($count>0){
//                 header('location: index.php?msg=dup');
//                 die();
//             }
        
//         $sql = "INSERT INTO `jobsapplied` "
//                 . "(`id`, `date`, `pid`, `sid`, `status`) "
//                 . "VALUES (NULL, CURRENT_DATE(), '$pid', '$sid', 'Applied');";
//         if ($conn->query($sql) === TRUE) {
       
//                 header('location: jobs.php?msg=success');
                
//             }else{
//                 header('location: jobs.php?msg=failed');
//             }
//     }else{
//         header('location:index.php?msg=login');
//     }
// }

if(isset($_GET['id'])){
    include 'connect.php';
    $pid = $_GET['id'];
    session_start();

    if (isset($_SESSION['sid'])){
        $sid = $_SESSION['sid'];

        $sql = "select * from jobsapplied where pid='$pid' and sid='$sid';";
        $result=$conn->query($sql);
        $count=$result->num_rows;
        if($count>0){
            header('location: index.php?msg=dup');
            die();
        }

        $sql = "SELECT eid FROM logpost WHERE pid='$pid';";
        $result = $conn->query($sql);
        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $eid = $row['eid'];
            echo $eid;
            

            // Get employer email
            $sql = "SELECT email FROM employer WHERE id='$eid'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $email = $row['email'];
                
                $sql = "INSERT INTO jobsapplied (id, date, pid, sid, status) 
                        VALUES (NULL, CURRENT_DATE(), '$pid', '$sid', 'Applied');";
                if ($conn->query($sql) === TRUE) {
                    sendBrevoEmail($email, $sid);
                    header('location: jobs.php?msg=success');
                    exit();
                } else {
                    header('location: jobs.php?msg=failed');
                    exit();
                }
            }
        }
    } else{
      header('location: index.php?msg=dup');
      die();
    }
}

function sendBrevoEmail($email, $sid) {
    $apiKey = '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'; // Replace with your Brevo API key
    $url = "https://api.brevo.com/v3/smtp/email";
  
    $data = [
      "sender" => [
        "name" => "Job Portal",
        "email" => "razonmarknicholas.cdlb@gmail.com"
      ],
      "to" => [
        ["email" => $email]
      ],
      "subject" => "New Job Application Received",
      "htmlContent" => "
        <p>Hello,</p>
        <p>A new job application has been submitted for one of your posted jobs.</p>
        <p>Please log in to your portal to review the application.</p>
      "
    ];
  
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Content-Type: application/json",
      "api-key: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
      error_log('Brevo API Error: ' . curl_error($ch));
    }
    curl_close($ch);
  }

?>