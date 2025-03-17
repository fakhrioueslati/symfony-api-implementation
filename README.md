# Symfony API Implementation
This project demonstrates the implementation of various backend features, providing basic API functionalities for managing resources. It includes operations for creating, reading, updating, and deleting resources, designed to be scalable for future features.

## Requirements
- PHP >= 8.1
- Symfony 6.4
- Composer
- MySQL or SQLite (or any other database supported by Doctrine ORM)

## Installation

1. **Clone the repository:**
    ```bash
    git clone <repository-url>
    ```
    Replace `<repository-url>` with the URL of the repository you want to clone.

2. **Navigate to the project directory:**
    ```bash
    cd symfony-api-implementation
    ```

3. **Install dependencies:**
    ```bash
    composer install
    ```

4. **Create the database:**
    ```bash
    php bin/console doctrine:database:create
    ```

5. **Run migrations (if needed):**
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

6. **Start the Symfony server (or use a PHP built-in server):**
    ```bash
    symfony server:start
    ```
    Or with PHP built-in server:
    ```bash
    php -S 127.0.0.1:8000 -t public
    ```

## Usage
Open your browser or use a tool like Postman to interact with the API at `http://127.0.0.1:8000/api`. The API provides endpoints for basic resource management.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
