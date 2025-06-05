# MLM ROI Platform

A powerful MLM (Multi-Level Marketing) platform with ROI (Return on Investment) features, built with PHP and MySQL.

## Features

- User authentication and authorization
- KYC (Know Your Customer) verification
- Investment plans with configurable ROI
- Daily ROI payments
- 5-level referral system
- Wallet system with deposits and withdrawals
- Admin dashboard with comprehensive management features

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/mlm-roi-platform.git
cd mlm-roi-platform
```

2. Install dependencies:
```bash
composer install
```

3. Create a MySQL database and import the schema:
```bash
mysql -u root -p
CREATE DATABASE advanced_mlm_roi;
exit;
mysql -u root -p advanced_mlm_roi < database/schema.sql
```

4. Configure the application:
   - Copy `includes/config.example.php` to `includes/config.php`
   - Update the database credentials and other settings in `config.php`

5. Set up the required directories with proper permissions:
```bash
mkdir -p public/uploads/{kyc,deposits}
chmod -R 755 public/uploads
```

6. Set up the cron job to run automated tasks:
```bash
# Add this line to your crontab (crontab -e)
0 0 * * * /usr/bin/php /path/to/your/installation/cron.php
```

## Directory Structure

```
├── app/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── Controller.php
│   │   ├── DashboardController.php
│   │   ├── InvestmentController.php
│   │   └── WalletController.php
│   └── Models/
│       ├── Investment.php
│       ├── InvestmentPlan.php
│       ├── Model.php
│       └── User.php
├── database/
│   └── schema.sql
├── includes/
│   ├── auth.php
│   └── config.php
├── public/
│   ├── uploads/
│   │   ├── deposits/
│   │   └── kyc/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── index.php
├── views/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── investments.php
│   │   ├── plans.php
│   │   └── users.php
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── forgot-password.php
│   │   └── reset-password.php
│   ├── dashboard/
│   │   ├── index.php
│   │   ├── investments.php
│   │   ├── profile.php
│   │   ├── referrals.php
│   │   └── wallet.php
│   └── layouts/
│       └── main.php
├── composer.json
├── cron.php
└── README.md
```

## Usage

### User Features

1. Registration and Login
   - Users can register with username, email, and password
   - Optional referral code for MLM structure
   - Email verification (if enabled)
   - Password reset functionality

2. KYC Verification
   - Users must submit KYC documents
   - Supported documents: ID card, passport, driver's license
   - Admin approval required

3. Investment Management
   - View available investment plans
   - Make new investments
   - Track active investments
   - View ROI payments
   - Cancel investments (if allowed)

4. Wallet Management
   - View wallet balance
   - Deposit funds
   - Withdraw funds
   - View transaction history

5. Referral System
   - View referral tree
   - Track referral earnings
   - Share referral code

### Admin Features

1. Dashboard
   - Overview of platform statistics
   - Pending KYC verifications
   - Pending deposits and withdrawals
   - Recent activities

2. User Management
   - View all users
   - View user details
   - Manage user status
   - View user investments and earnings

3. Investment Plans
   - Create new plans
   - Edit existing plans
   - Activate/deactivate plans
   - View plan statistics

4. Investment Management
   - View all investments
   - Track ROI payments
   - Monitor investment status

5. Financial Management
   - Approve/reject deposits
   - Approve/reject withdrawals
   - View transaction history

## Security

- Password hashing using PHP's password_hash()
- CSRF protection
- SQL injection prevention using PDO
- XSS protection
- Session security
- Input validation and sanitization

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please create an issue in the GitHub repository or contact us at support@example.com. 