<?php
require_once 'includes/config.php';

use App\Controllers\InvestmentController;

// Set script execution time to unlimited
set_time_limit(0);

// Prevent script from being accessed via web browser
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

try {
    $investmentController = new InvestmentController($pdo);
    
    // Process ROI payments
    echo "Processing ROI payments...\n";
    $investmentController->processROI();
    echo "ROI payments processed successfully\n";
    
    // Complete matured investments
    echo "Completing matured investments...\n";
    $investmentController->completeMaturedInvestments();
    echo "Matured investments completed successfully\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log($e->getMessage());
} 