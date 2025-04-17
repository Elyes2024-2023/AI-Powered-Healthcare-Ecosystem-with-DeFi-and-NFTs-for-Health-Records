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

- PHP 8.1 or higher
- Composer (PHP package manager)
- Node.js and npm
- MySQL/PostgreSQL
- MetaMask wallet
- Python 3.8+ (for AI services)
- Go 1.16+ (for blockchain services)
- Rust (latest stable version)

## Installation

### 1. Install Composer
**Windows:**
- Download the Composer installer from https://getcomposer.org/download/
- Run the installer and follow the installation wizard
- Verify installation by running: `composer --version`

### 2. Install PHP
**Windows:**
- Download PHP from https://windows.php.net/download/
- Extract to C:\php
- Add PHP to system PATH
- Copy php.ini-development to php.ini
- Enable required extensions in php.ini:
  ```
  extension=openssl
  extension=pdo_mysql
  extension=mbstring
  extension=fileinfo
  extension=gd
  ```

### 3. Project Setup
```bash
# Clone the repository
git clone https://github.com/Elyes2024-2023/AI-Powered-Healthcare-Ecosystem-with-DeFi-and-NFTs-for-Health-Records.git

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
│   ├── app/             # Application code
│   ├── config/          # Configuration files
│   ├── database/        # Database migrations and seeds
│   ├── resources/       # Views and assets
│   └── routes/          # Route definitions
├── symfony/             # AI & Blockchain backend (Symfony)
│   ├── src/             # Source code
│   ├── config/          # Configuration files
│   └── tests/           # Test files
├── frontend/            # React.js frontend
│   ├── src/             # Source code
│   ├── public/          # Public assets
│   └── package.json     # Dependencies
├── contracts/           # Ethereum smart contracts
│   ├── HealthRecordNFT.sol
│   └── InsurancePolicy.sol
├── ai-services/         # Python AI services
│   ├── models/          # AI models
│   └── api/             # API endpoints
└── blockchain-services/ # Go blockchain services
    ├── cmd/             # Command line tools
    └── internal/        # Internal packages
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Author

**ELYES**
- GitHub: [@Elyes2024-2023](https://github.com/Elyes2024-2023)
- Email: [Your Email]

## Copyright

Copyright (c) 2024-2025 ELYES. All rights reserved.

---

*Done by ELYES* 