<?php
require_once 'includes/config.php';
require_once 'includes/Auth.php';
require_once 'includes/Deposit.php';

// Check if user is logged in
$auth = new Auth($pdo);
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$deposit = new Deposit($pdo);

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $transactionId = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_STRING);
    
    // Handle file upload
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/deposit_proofs/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('proof_') . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $filePath)) {
            if ($deposit->createRequest($userId, $amount, $transactionId, $filePath)) {
                $message = 'Deposit request submitted successfully!';
            } else {
                $message = 'Error submitting deposit request.';
            }
        } else {
            $message = 'Error uploading proof image.';
        }
    } else {
        $message = 'Please upload payment proof.';
    }
}

// Get user's deposit history
$depositHistory = $deposit->getUserRequests($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Funds</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Deposit Funds</h1>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Bank Details</h2>
            <div class="bank-details">
                <p><strong>Bank Name:</strong> YOUR BANK NAME</p>
                <p><strong>Account Number:</strong> YOUR ACCOUNT NUMBER</p>
                <p><strong>Account Name:</strong> YOUR ACCOUNT NAME</p>
                <p><strong>IFSC Code:</strong> YOUR IFSC CODE</p>
            </div>
        </div>
        
        <div class="card">
            <h2>Submit Deposit Request</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="transaction_id">Transaction ID/Reference</label>
                    <input type="text" id="transaction_id" name="transaction_id" required>
                </div>
                
                <div class="form-group">
                    <label for="proof_image">Payment Proof (Screenshot/Image)</label>
                    <input type="file" id="proof_image" name="proof_image" accept="image/*" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Deposit History</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($depositHistory as $deposit): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i', strtotime($deposit['created_at'])); ?></td>
                            <td><?php echo number_format($deposit['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($deposit['transaction_id']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($deposit['status']); ?>">
                                    <?php echo ucfirst($deposit['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html> 