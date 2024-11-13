<?php
// dform.php

include 'db_conn.php';

// Fetch data for the dashboard
$sql = "SELECT id, donation_date, donor_name, item_name, status, transfer_status, updated_date FROM donations";
$result = $db->query($sql);

// Fetch summary counts
$totalForms = $db->query("SELECT COUNT(*) AS total FROM donations")->fetch_assoc()['total'];
$acceptedForms = $db->query("SELECT COUNT(*) AS accepted FROM donations WHERE status = 'accepted'")->fetch_assoc()['accepted'];
$rejectedForms = $db->query("SELECT COUNT(*) AS rejected FROM donations WHERE status = 'rejected'")->fetch_assoc()['rejected'];
$totalDonationForms = $db->query("SELECT COUNT(*) AS total FROM donation_form")->fetch_assoc()['total'];
$totalLendingForms = $db->query("SELECT COUNT(*) AS total FROM lending_form")->fetch_assoc()['total'];

// update.php


// donateview.php



$donationFormData = $db->query("SELECT * FROM donation_form WHERE id = 105");

$first_name = '';
$last_name = '';
$age = '';
$sex = '';
$email = '';
$phone = '';
$org = '';
$province = '';
$city = '';
$brgy = '';
$street = '';
$title = '';
$artifact_description = '';
$acquisition_details = '';
$additional_info = '';
$narrative = '';
$image_url = '';

if ($donationFormData && $donationFormData->num_rows > 0) {
    $row = $donationFormData->fetch_assoc();
    
    $first_name = htmlspecialchars($row['first_name']);
    $last_name = htmlspecialchars($row['last_name']);
    $age = (int) $row['age'];
    $sex = htmlspecialchars($row['sex']);
    $email = htmlspecialchars($row['email']);
    $phone = htmlspecialchars($row['phone']);
    $org = htmlspecialchars($row['organization']);
    $province = htmlspecialchars($row['province']);
    $city = htmlspecialchars($row['city']);
    $brgy = htmlspecialchars($row['barangay']);
    $street = htmlspecialchars($row['street']);
    $title = htmlspecialchars($row['artifact_title']);
    $artifact_description = htmlspecialchars($row['artifact_description']);
    $acquisition_details = htmlspecialchars($row['acquisition_details']);
    $additional_info = htmlspecialchars($row['additional_info']);
    $narrative = htmlspecialchars($row['narrative']);
    $image_url = 'img/' . htmlspecialchars($row['artifact_images']);
} else {
    echo "No artifact data found for the specified donor ID.";
}

$db->close();
?>
