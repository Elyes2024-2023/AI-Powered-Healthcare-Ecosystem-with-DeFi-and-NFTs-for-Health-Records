# AI-Powered Healthcare Ecosystem

A comprehensive healthcare platform that integrates AI-powered diagnosis, DeFi-powered health insurance, and NFT-backed health records.

## Features

- AI-Powered Symptom Checker
- Decentralized Health Insurance via DeFi
- NFT-based Health Records
- IoT Integration for Real-time Health Monitoring
- Smart Contract Automation
- Enhanced User Privacy and Security

## Prerequisites

1. PHP 8.1 or higher
2. Composer (PHP package manager)
3. Node.js and npm
4. MySQL/PostgreSQL
5. MetaMask wallet
6. Python 3.8+ (for AI services)
7. Go 1.16+ (for blockchain services)
8. Rust (latest stable version)

## Installation

### 1. Install Composer

Windows:
1. Download the Composer installer from https://getcomposer.org/download/
2. Run the installer and follow the installation wizard
3. Verify installation by running: `composer --version`

### 2. Install PHP

Windows:
1. Download PHP from https://windows.php.net/download/
2. Extract to C:\php
3. Add PHP to system PATH
4. Copy php.ini-development to php.ini
5. Enable required extensions in php.ini:
   - extension=openssl
   - extension=pdo_mysql
   - extension=mbstring
   - extension=fileinfo
   - extension=gd

### 3. Project Setup

```bash
# Clone the repository
git clone [repository-url]

# Install Laravel dependencies
composer install

# Install frontend dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Start the development server
php artisan serve
```

## Project Structure

```
healthcare-ecosystem/
├── laravel/              # User-facing backend (Laravel)
│   └── ...
├── symfony/              # AI & Blockchain backend (Symfony)
│   └── ...
├── frontend/            # React.js frontend
│   └── ...
├── contracts/           # Ethereum smart contracts
│   └── ...
├── ai-services/         # Python AI services
│   └── ...
└── blockchain-services/ # Go blockchain services
    └── ...
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details 