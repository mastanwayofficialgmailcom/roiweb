<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Auth.php';
require_once __DIR__ . '/app/Investment.php';

$auth = App\Auth::getInstance($conn);
$investment = App\Investment::getInstance($conn);

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];
    
    switch ($action) {
        case 'login':
            $response = $auth->login($_POST['username'], $_POST['password']);
            break;
            
        case 'register':
            $response = $auth->register($_POST);
            break;
            
        case 'invest':
            if ($auth->isLoggedIn()) {
                $response = $investment->invest(
                    $_SESSION['user_id'],
                    $_POST['plan_id'],
                    $_POST['amount']
                );
            }
            break;
            
        case 'get_user_investments':
            if ($auth->isLoggedIn()) {
                $response = [
                    'success' => true,
                    'investments' => $investment->getUserInvestments($_SESSION['user_id'])
                ];
            }
            break;
    }
    
    echo json_encode($response);
    exit;
}

// Get current user if logged in
$currentUser = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    
    <!-- Modern UI Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .investment-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
        }
        
        .investment-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card {
            background: linear-gradient(45deg, #4158D0, #C850C0);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
        
        .btn-gradient {
            background: linear-gradient(45deg, #4158D0, #C850C0);
            border: none;
            color: white;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(45deg, #3147B0, #A740A0);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><?= APP_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#investment-plans">Investment Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#referral">Referral Program</a>
                    </li>
                </ul>
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($currentUser['username']) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#dashboard">Dashboard</a></li>
                            <li><a class="dropdown-item" href="#profile">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logout">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-5">
        <?php if ($currentUser): ?>
            <!-- Dashboard -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card mb-3">
                        <h3 class="h6 mb-2">Wallet Balance</h3>
                        <h4 class="mb-0">$<?= number_format($currentUser['wallet_balance'], 2) ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card mb-3">
                        <h3 class="h6 mb-2">Total Earnings</h3>
                        <h4 class="mb-0">$<?= number_format($currentUser['total_earnings'], 2) ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card mb-3">
                        <h3 class="h6 mb-2">Active Investments</h3>
                        <h4 class="mb-0">$<?= number_format($currentUser['total_investments'], 2) ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card mb-3">
                        <h3 class="h6 mb-2">Team Size</h3>
                        <h4 class="mb-0"><?= number_format($currentUser['total_team_size']) ?></h4>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Investment Plans -->
        <section id="investment-plans" class="mb-5">
            <h2 class="mb-4">Investment Plans</h2>
            <div class="row">
                <?php foreach ($investment->getPlans() as $plan): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card investment-card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($plan['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($plan['description']) ?></p>
                                <ul class="list-unstyled">
                                    <li><i class="mdi mdi-check text-success"></i> <?= $plan['roi_percentage'] ?>% ROI</li>
                                    <li><i class="mdi mdi-check text-success"></i> <?= $plan['duration_days'] ?> Days</li>
                                    <li><i class="mdi mdi-check text-success"></i> Min: $<?= number_format($plan['minimum_amount']) ?></li>
                                    <li><i class="mdi mdi-check text-success"></i> Max: $<?= number_format($plan['maximum_amount']) ?></li>
                                </ul>
                                <?php if ($currentUser): ?>
                                    <button class="btn btn-gradient w-100 invest-btn" data-plan-id="<?= $plan['id'] ?>">
                                        Invest Now
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-gradient w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        Login to Invest
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="mb-5">
            <h2 class="mb-4">How It Works</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="mdi mdi-account-plus display-4 text-primary mb-3"></i>
                            <h5>1. Create Account</h5>
                            <p>Register and complete your profile verification</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="mdi mdi-cash-multiple display-4 text-primary mb-3"></i>
                            <h5>2. Choose Plan & Invest</h5>
                            <p>Select your preferred investment plan and start earning</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="mdi mdi-chart-line display-4 text-primary mb-3"></i>
                            <h5>3. Earn & Withdraw</h5>
                            <p>Receive daily ROI and withdraw your earnings</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Referral Program -->
        <section id="referral" class="mb-5">
            <h2 class="mb-4">Referral Program</h2>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Multi-Level Referral System</h5>
                            <p>Earn commissions from your referrals' investments up to 10 levels deep!</p>
                            <ul>
                                <li>Level 1: 10% Commission</li>
                                <li>Level 2: 5% Commission</li>
                                <li>Level 3: 3% Commission</li>
                                <li>Level 4-10: 1% Commission</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <?php if ($currentUser): ?>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Your Referral Link</h6>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="<?= APP_URL ?>?ref=<?= $currentUser['referral_code'] ?>" readonly>
                                            <button class="btn btn-primary copy-btn" type="button">Copy</button>
                                        </div>
                                        <div class="mt-3">
                                            <small>Direct Referrals: <?= $currentUser['direct_referrals'] ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#registerModal">
                                        Register to Get Referral Link
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Username or Email</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Modal -->
    <div class="modal fade" id="investModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Make Investment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="investForm">
                        <input type="hidden" name="plan_id">
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amount" required>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100">Confirm Investment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Login Form
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                $.post('', {
                    action: 'login',
                    username: $('[name="username"]').val(),
                    password: $('[name="password"]').val()
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            });

            // Register Form
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                if ($('[name="password"]').val() !== $('[name="confirm_password"]').val()) {
                    alert('Passwords do not match');
                    return;
                }
                $.post('', {
                    action: 'register',
                    username: $('[name="username"]').val(),
                    email: $('[name="email"]').val(),
                    full_name: $('[name="full_name"]').val(),
                    password: $('[name="password"]').val()
                }, function(response) {
                    if (response.success) {
                        $('#registerModal').modal('hide');
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                });
            });

            // Investment
            $('.invest-btn').on('click', function() {
                $('[name="plan_id"]').val($(this).data('plan-id'));
                $('#investModal').modal('show');
            });

            $('#investForm').on('submit', function(e) {
                e.preventDefault();
                $.post('', {
                    action: 'invest',
                    plan_id: $('[name="plan_id"]').val(),
                    amount: $('[name="amount"]').val()
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            });

            // Copy Referral Link
            $('.copy-btn').on('click', function() {
                var input = $(this).prev('input')[0];
                input.select();
                document.execCommand('copy');
                alert('Referral link copied!');
            });

            // Logout
            $('#logout').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    location.href = '?logout=1';
                }
            });
        });
    </script>
</body>
</html> 