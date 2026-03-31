<?php
// client/process_payment.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/NotificationModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PaymentModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to make a payment']);
    exit;
}

$user_id = $_SESSION['user_id'];
$propertyModel = new PropertyModel();
$notificationModel = new NotificationModel();
$userModel = new UserModel();
$paymentModel = new PaymentModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'initiate_payment') {
        $property_id = intval($_POST['property_id'] ?? 0);
        $phone = $_POST['phone_number'] ?? '';
        $amount = floatval($_POST['amount'] ?? 0);
        
        // Get property and landlord details
        $property = $propertyModel->getPropertyById($property_id);
        if (!$property) {
            echo json_encode(['success' => false, 'message' => 'Property not found']);
            exit;
        }
        
        $landlord = $userModel->getUserById($property['landlord_id']);
        if (!$landlord) {
            echo json_encode(['success' => false, 'message' => 'Landlord not found']);
            exit;
        }
        
        // Validate phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            $phone = '254' . substr($phone, 1);
        }
        if (substr($phone, 0, 3) != '254') {
            $phone = '254' . $phone;
        }
        
        // Create payment record
        $paymentData = [
            'property_id' => $property_id,
            'tenant_id' => $user_id,
            'landlord_id' => $property['landlord_id'],
            'amount' => $amount,
            'phone_number' => $phone,
            'status' => 'pending'
        ];
        
        $payment_id = $paymentModel->createPayment($paymentData);
        
        // Send STK Push via Safaricom API
        $result = initiateSTKPush($phone, $amount, $property['property_name'], $payment_id);
        
        if ($result['success']) {
            // Create notification for landlord
            $notificationModel->create(
                $property['landlord_id'],
                'payment_initiated',
                "Payment of KES " . number_format($amount) . " initiated for {$property['property_name']} by tenant",
                $property_id
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'STK Push sent. Check your phone to complete payment.',
                'checkout_request_id' => $result['checkout_request_id'],
                'payment_id' => $payment_id
            ]);
        } else {
            $paymentModel->updatePaymentStatus($payment_id, 'failed');
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to initiate payment'
            ]);
        }
        exit;
    }
    
    if ($action === 'check_status') {
        $checkout_request_id = $_POST['checkout_request_id'] ?? '';
        $payment_id = intval($_POST['payment_id'] ?? 0);
        
        $result = checkPaymentStatus($checkout_request_id);
        
        if ($result['success'] && $result['status'] === 'completed') {
            $paymentModel->updatePaymentStatus($payment_id, 'completed', $result['receipt_number']);
            
            // Get payment details
            $payment = $paymentModel->getPaymentById($payment_id);
            if ($payment) {
                // Create notification for landlord
                $notificationModel->create(
                    $payment['landlord_id'],
                    'payment_received',
                    "Payment of KES " . number_format($payment['amount']) . " received for property",
                    $payment['property_id']
                );
            }
            
            echo json_encode([
                'success' => true,
                'status' => 'completed',
                'message' => 'Payment completed successfully!'
            ]);
        } elseif ($result['success'] && $result['status'] === 'pending') {
            echo json_encode([
                'success' => true,
                'status' => 'pending',
                'message' => 'Payment is still processing'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'status' => 'failed',
                'message' => $result['message'] ?? 'Payment failed'
            ]);
        }
        exit;
    }
    
    if ($action === 'send_rent_request') {
        $property_id = intval($_POST['property_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $move_date = $_POST['move_date'] ?? '';
        $message = trim($_POST['message'] ?? '');
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($phone) || empty($move_date)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        // Get property details
        $property = $propertyModel->getPropertyById($property_id);
        if (!$property) {
            echo json_encode(['success' => false, 'message' => 'Property not found']);
            exit;
        }
        
        // Create notification for landlord
        $notificationMessage = "New rent request from $name for {$property['property_name']}\n";
        $notificationMessage .= "Email: $email\n";
        $notificationMessage .= "Phone: $phone\n";
        $notificationMessage .= "Move-in Date: $move_date\n";
        $notificationMessage .= "Message: $message";
        
        $notificationModel->create(
            $property['landlord_id'],
            'rent_request',
            $notificationMessage,
            $property_id
        );
        
        // Also create notification for tenant
        $notificationModel->create(
            $user_id,
            'rent_request_sent',
            "Your rent request for {$property['property_name']} has been sent to the landlord",
            $property_id
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Rent request sent successfully! The landlord will contact you soon.'
        ]);
        exit;
    }
}

// M-Pesa API Functions
function initiateSTKPush($phone, $amount, $property_name, $payment_id) {
    // Safaricom API credentials (from environment variables or config)
    $consumer_key = 'YOUR_CONSUMER_KEY'; // Replace with your actual key
    $consumer_secret = 'YOUR_CONSUMER_SECRET'; // Replace with your actual secret
    $passkey = 'YOUR_PASSKEY'; // Replace with your actual passkey
    $shortcode = '174379'; // Sandbox shortcode
    
    // Get access token
    $token = getAccessToken($consumer_key, $consumer_secret);
    if (!$token) {
        return ['success' => false, 'message' => 'Failed to get access token'];
    }
    
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);
    $callback_url = 'https://yourdomain.com/Landlord-MGT/client/mpesa_callback.php';
    
    $curl_post_data = [
        'BusinessShortCode' => $shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => round($amount),
        'PartyA' => $phone,
        'PartyB' => $shortcode,
        'PhoneNumber' => $phone,
        'CallBackURL' => $callback_url,
        'AccountReference' => 'Rent-' . $payment_id,
        'TransactionDesc' => 'Rent Payment - ' . substr($property_name, 0, 20)
    ];
    
    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $result = json_decode($response, true);
        if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
            return [
                'success' => true,
                'checkout_request_id' => $result['CheckoutRequestID'],
                'message' => 'STK Push sent successfully'
            ];
        }
        return ['success' => false, 'message' => $result['errorMessage'] ?? 'Unknown error'];
    }
    
    return ['success' => false, 'message' => 'Failed to connect to M-Pesa API'];
}

function getAccessToken($consumer_key, $consumer_secret) {
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return $result['access_token'] ?? null;
}

function checkPaymentStatus($checkout_request_id) {
    // Implementation for checking payment status
    // This would query the M-Pesa API
    return ['success' => true, 'status' => 'completed', 'receipt_number' => 'TEST123456'];
}
?>