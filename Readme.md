# Mini Aspire Loan Management System

Welcome to the Mini Aspire Loan Management System! This document will guide you through the setup process, application structure, and testing procedures.

## Table of Contents

- [About the Application](#about-the-application)
- [Getting Started](#getting-started)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Database Setup](#database-setup)
- [Application Structure](#application-structure)
- [Features](#features)
  - [Loan Application](#loan-application)
  - [Loan Approval](#loan-approval)
  - [Loan Repayment](#loan-repayment)
- [Future Enhancements](#future-enhancements)
- [Testing](#testing)
  - [Unit Tests](#unit-tests)
  - [Feature Tests](#feature-tests)
- [Conclusion](#conclusion)

## About the Application

The Mini Aspire Loan Management System streamlines the process of loan applications, approvals, and repayments. Authenticated users can easily apply for loans, track their loan status, and manage repayments conveniently.

## Getting Started

Follow these steps to set up the application on your local machine.

### Installation

To install the application's dependencies, run the following command in your terminal:
```bash 
docker-compose up -d
```

```bash
composer install
```

### Configuration

1. Copy the `.env.example` file to create a `.env` file:

```bash
cp .env.example .env
```

2. Configure your database connection settings in the `.env` file.

### Database Setup

1. Create a new database schema in your chosen database system.

2. Run database migrations to create the necessary tables:

```bash
php artisan migrate
```

3. Seed the database with sample data:

```bash
php artisan db:seed
```

## Application Structure

The application follows a structured architecture to maintain separation of concerns and ensure maintainability.

- `src/Domain`
  - `Entities`: Define business rules and behavior for loan-related entities.
  - `DTOs`: Transfer data between different layers or components.
  - `Services`: Implement domain logic for loan application, approval, and repayment.
  
- `src/Http`
  - Handles the user interface and presentation of loan application and repayment functionality.
  
- `src/Infrastructure`
  - Provides interaction with domain entities without exposing data source details.
  - Utilizes repository interfaces to interact with loan-related data.

## Features

### Loan Application

Authenticated users can apply for loans by providing details such as the "amount required" and "loan term."

### Loan Approval

Admin users can review and approve loan applications submitted by users.

### Loan Repayment

Users with approved loans can submit weekly repayments. Repayment functionality is simplified for demonstration purposes.

## Future Enhancements

- **Unit test**: Implement unit test.
- **User Dashboard**: Create a dashboard for users to view active loans, repayment status, and upcoming payment reminders.
- **Interest Calculation**: Introduce interest rates for late or missed loan repayments.
- **Loan Analytics**: Provide users insights into their loan repayment history and progress.
- **User Profile Management**: Allow users to update their personal information and contact details.
- **Secure Payments**: Implement a secure payment gateway for processing loan repayments.
- **Multi-Language Support**: Offer users the option to switch between different languages.
- **Mobile App Version**: Consider developing a mobile app version for improved accessibility.
- **Document Uploads**: Allow users to upload supporting documents during the loan application process.
- **Loan Term Options**: Offer users different loan term options (e.g., weekly, bi-weekly, monthly).
- **Customer Support Chat**: Integrate a chatbot or live chat for instant customer support.

## Testing

The application includes unit tests to ensure the correctness of individual components and business logic. Run the following command to execute unit tests:

```bash
php artisan test
```


## Conclusion

You've successfully set up the Mini Aspire Loan Management System and learned about its architecture and testing procedures. As the application evolves, we plan to incorporate future enhancements to provide an even more comprehensive loan management solution.

For any inquiries or feedback, please don't hesitate to reach out.
