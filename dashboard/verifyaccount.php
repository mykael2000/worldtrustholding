<?php
session_start();
include("function.php"); // DB connection

$userId = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function uploadFile($input, $folder) {
        if (!isset($_FILES[$input]) || $_FILES[$input]['error'] !== 0) {
            return null;
        }

        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Invalid file type uploaded.");
        }

        if ($_FILES[$input]['size'] > 5 * 1024 * 1024) {
            die("File size exceeds 5MB.");
        }

        $filename = uniqid() . "." . $ext;
        $path = "uploads/kyc/" . $filename;

        if (!is_dir("uploads/kyc")) {
            mkdir("uploads/kyc", 0755, true);
        }

        move_uploaded_file($_FILES[$input]['tmp_name'], $path);

        return $path;
    }

    // Upload files
    $frontImg = uploadFile('frontimg', 'kyc');
    $backImg  = uploadFile('backimg', 'kyc');
    $photoImg = uploadFile('photo', 'kyc');

    // Sanitize inputs
    $data = array_map(fn($v) => mysqli_real_escape_string($conn, trim($v)), $_POST);

    // Insert KYC
    $sql = "
    INSERT INTO kyc_submissions (
        user_id, full_name, email, phone, title, gender, zipcode, dob,
        state_number, account_type, employment_type, income_range,
        address, city, state, country,
        kin_name, kin_address, relationship, kin_age,
        document_type, document_front, document_back, passport_photo
    ) VALUES (
        '{$userId}', '{$data['name']}', '{$data['email']}', '{$data['phone']}',
        '{$data['title']}', '{$data['gender']}', '{$data['zipcode']}', '{$data['dob']}',
        '{$data['statenumber']}', '{$data['accounttype']}', '{$data['employer']}', '{$data['income']}',
        '{$data['address']}', '{$data['city']}', '{$data['state']}', '{$data['country']}',
        '{$data['kinname']}', '{$data['kinaddress']}', '{$data['relationship']}', '{$data['age']}',
        '{$data['document_type']}', '{$frontImg}', '{$backImg}', '{$photoImg}'
    )";

    if (mysqli_query($conn, $sql)) {

        // Update user KYC status
        mysqli_query(
            $conn,
            "UPDATE users SET kyc_status='pending' WHERE id='{$userId}'"
        );

        header("Location: verify.php?success=1");
        exit;

    } else {
        die("KYC submission failed.");
    }
}
