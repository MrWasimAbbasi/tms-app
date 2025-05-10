# Laravel Translation Management System Setup Guide

Follow these instructions to set up and run the **Laravel Translation Management System** on your local machine. This guide is designed to be simple and easy to follow.

---

## 1. **Clone the Project**

### First, clone the project to your local machine.
 
1. **Open a terminal/command prompt** and run the following command to clone the project:

    ```bash
    git clone https://github.com/MrWasimAbbasi/tms-app.git
    ```

   This will create a folder named `tms-app` with all the project files.

---

## 2. **Build and Start the Project Using Docker**

Now that Docker is installed, let's set up the project.

1. **Open a terminal/command prompt**.

2. Navigate to the project folder you cloned earlier. For example:

    ```bash
    cd tms-app
    ```

3. **Build and start the Docker containers** by running the following command:

    ```bash
    docker compose up --build -d
    ```

4. After a few minutes, your containers should be up and running.

---

## 4. **Install Composer (Inside the Docker Container)**

Composer is a tool used for managing PHP dependencies. We need to install Composer inside the Docker container.

1. **Enter the Docker container** by running the following command:

    ```bash
    docker exec -it app bash
    ```

2. **Install the project dependencies** using Composer:

    ```bash
    composer install
    ```

   This might take a couple of minutes to complete.

---

## 5. **Run Database Migrations With Seeders**

Laravel uses migrations to set up the database structure. You need to run the migrations to create the required database tables.

1. Inside the Docker container (if you're not already inside), run the following command:

    ```bash
    php artisan migrate --seed
    ```

## 7. **Access the Application**

The application should now be up and running. To access it:

1. **Open a web browser** and go to:

    ```
    http://localhost:8000
    ```

   This will load the application’s main page.

2. **Access the API documentation** (Swagger) by going to:

    ```
    http://localhost:8000/api/documentation
    ```

   Here, you can interact with the API and test various endpoints.

---

## 8. **Running Tests (Optional)**

If you want to run tests to ensure everything is working correctly:

1. **Inside the Docker container**, run the following command:

    ```bash
    php artisan test
    ```

---
