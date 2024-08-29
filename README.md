# Event Management App in Laravel 11

This repository contains a Laravel application based on Domain Driven Design principle.

## Prerequisites

-   PHP 8.2^

## Getting Started

1. **Clone the Repository**

    ```bash

    git clone https://github.com/shamamit11/event-manager

    cd event-manager

    ```

2. **Installation**

-   Copy the .env.example to .env

    ```bash

    cp .env.example .env

    ```

-   Run composer to install the dependencies

    ```bash

    composer install


    ```

-   Create a MySQL database and update the database credentials in your .env file and run the migration

    ```bash

    php artisan migrate

    ```

3. **Run the application**

    ```bash

    php artisan serve

    ```

4. **Run the application using Laravel Sail**

-   In your .env file, make changes as below:

        DB_CONNECTION=mysql
        DB_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=events
        DB_USERNAME=sail
        DB_PASSWORD=password

-   Make sure you have Docker Installed and Running, then run the following commands:

    ```bash

    ./vendor/bin/sail up

    ./vendor/bin/sail artisan migrate

    ```

-   You can now browse the application using the address below:

    ```bash

    http://localhost:8080/

    http://localhost:8080/request-docs

    ```

5. **Start / Stop Sail**

    ```bash

    ./vendor/bin/sail up

    ./vendor/bin/sail stop

    ```

6. **Access the Application**

-   This application uses Laravel Request Docs for API Documentation.

-   Open your web browser and navigate to http://localhost:8000/request-docs to view the API.

-   You can use postman to test the api endpoints.

5. **Running PHP Analyse Tool**

-   This application use Larastan (based on PHPStan) for code analysis

    ```bash

    ./vendor/bin/phpstan analyse --memory-limit=2G

    ```

6. **Running Tests**

    ```bash

    ./vendor/bin/pest

    ./vendor/bin/sail artisan test (if you are using docker)

    ```

# Payload Examples in JSON

## Creating an Event with Recurring Pattern

-   Endpoint: http://127.0.0.1:8000/api/events
-   Endpoint: http://localhost:8080/api/events (if you are using docker)

-   Method: Post

    ```bash

    {
        "title": "Example Event 01",
        "description": "Example Description 01",
        "start": "2024-08-20T10:00:00",
        "end": "2024-08-30T10:00:00",
        "recurring_pattern": {
            "frequency": "daily",
            "repeat_until": "2024-08-30T10:00:00",
        }
    }

    ```

## Creating an Event without Recurring Pattern

-   Endpoint: http://127.0.0.1:8000/api/events
-   Endpoint: http://localhost:8080/api/events (if you are using docker)

-   Method: Post

    ```bash

    {
        "title": "Example Event 02",
        "description": "Example Description 02",
        "start": "2024-09-10T10:00:00",
        "end": "2024-09-30T10:00:00"
    }

    ```

## Updating an Event

-   Endpoint: http://127.0.0.1:8000/api/events/1
-   Endpoint: http://localhost:8080/api/events/1 (if you are using docker)

-   Method: Put

    ```bash

    {
        "id": 1,
        "title": "Example Event 01 Edited",
        "description": "Example Description 01 Edited",
        "start": "2024-08-25T10:00:00",
        "end": "2024-08-30T10:00:00",
        "recurring_pattern": {
            "frequency": "daily",
            "repeat_until": "2024-08-30T10:00:00",
        }
    }

    ```

## List Events within the Date Range

-   Endpoint: http://127.0.0.1:8000/api/events/list?start=&end=&per_page=&page=
-   Endpoint: http://localhost:8080/api/events/list?start=&end=&per_page=&page= (if you are using docker)

-   Method: Get

    ```bash

    http://127.0.0.1:8000/api/events/list?start=2024-08-01T10:00:00&end=2024-08-30T10:00:00&per_page=10&page=1

    http://localhost:8080/api/events/list?start=2024-08-01T10:00:00&end=2024-08-30T10:00:00&per_page=10&page=1 (Docker)

    ```

## Delete Event

-   Endpoint: http://127.0.0.1:8000/api/event/1
-   Endpoint: http://localhost:8080/api/event/1 (if you are using docker)

-   Method: Delete

    ```bash

    http://127.0.0.1:8000/api/event/1

    http://localhost:8080/api/event/1 (Docker)

    ```
