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

4. **Access the Application**

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

    ```

## Payload Examples

# Creating an Event with Recurring Pattern

-   Endpoint: http://127.0.0.1:8000/api/events

-   Method: Post

    ```bash

    {
        "title": "Example Event 01",
        "description": "Example Description 01",
        "start": "2024-08-20T10:00:00",
        "end": "2024-08-30T10:00:00",
        "recurring_pattern": [
            "frequency": "daily",
            "repeat_until": "2024-08-30T10:00:00",
        ]
    }

    ```

# Creating an Event without Recurring Pattern

-   Endpoint: http://127.0.0.1:8000/api/events

-   Method: Post

    ```bash

    {
        "title": "Example Event 02",
        "description": "Example Description 02",
        "start": "2024-09-10T10:00:00",
        "end": "2024-09-30T10:00:00"
    }

    ```

# Updating an Event

-   Endpoint: http://127.0.0.1:8000/api/events/1

-   Method: Put

    ```bash

    {
        "id": 1,
        "title": "Example Event 01 Edited",
        "description": "Example Description 01 Edited",
        "start": "2024-08-25T10:00:00",
        "end": "2024-08-30T10:00:00",
        "recurring_pattern": [
            "frequency": "daily",
            "repeat_until": "2024-08-30T10:00:00",
        ]
    }

    ```

# List Events within the Date Range

-   Endpoint: http://127.0.0.1:8000/api/events/list?start=&end=&per_page=&page=

-   Method: Get

    ```bash

    http://127.0.0.1:8000/api/events/list?start=2024-08-01T10:00:00&end=2024-08-30T10:00:00&per_page=10&page=1

    ```

# Delete Event

-   Endpoint: http://127.0.0.1:8000/api/event/1

-   Method: Delete

    ```bash

    http://127.0.0.1:8000/api/event/1

    ```
