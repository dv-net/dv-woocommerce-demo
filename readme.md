#  WooCommerce DV.net Gateway 🛒

This repository contains a WordPress plugin that integrates the [DV.net](https://dv.net) payment gateway with WooCommerce. It serves as a demonstration of how to use the [`dv-net/dv-net-php-client`](https://github.com/dv-net/dv-net-php-client) library in a real-world application. 🚀

## How It Works ⚙️

The plugin integrates with WooCommerce to add **DV.net** as a payment option on the checkout page. When a customer selects this payment method, they are redirected to a secure payment page hosted by DV.net to complete their purchase. 💳

The plugin also exposes a webhook endpoint to receive notifications from DV.net about the payment status. When a payment is successfully confirmed, the plugin updates the corresponding order in WooCommerce. ✅

## Installation 🛠️

### Prerequisites

* Docker 🐳
* Docker Compose 🎶
* Composer 🎼

### Steps

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd <repository-directory>
    ```

2.  **Install PHP dependencies:**
    Navigate to the `dv-woocommerce` directory and run Composer to install the required dependencies.
    ```bash
    cd dv-woocommerce
    composer install
    ```

3.  **Set up the environment:**
    The project uses Docker for local development. A `docker-compose.yml` file is provided to set up a WordPress environment with the plugin pre-installed. You will need to create a `.env` file in the root of the project with the following variables:

    ```env
    COMPOSE_PROJECT_NAME=dv_woocommerce_dev
    MYSQL_ROOT_PASSWORD=rootpassword
    MYSQL_DATABASE=wordpress
    MYSQL_USER=wordpress
    MYSQL_PASSWORD=wordpress
    WP_HOME=http://localhost:8000
    WP_ADMIN_USER=admin
    WP_ADMIN_PASSWORD=password
    WP_ADMIN_EMAIL=admin@example.com
    ```

4.  **Start the development environment:**
    From the root of the project, run the following command:
    ```bash
    docker-compose up -d
    ```
    This will start a WordPress instance with the DV.net gateway plugin activated. ✨

## Configuration 🔧

Once the environment is running, you can configure the plugin from the WordPress admin panel:

1.  Log in to the WordPress admin panel at `http://localhost:8000/wp-admin` with the credentials you set in your `.env` file (default: `admin`/`password`).
2.  Navigate to **WooCommerce > Settings > Payments**.
3.  Find **DV.net Gateway** in the list of payment methods and click **Manage**.

You will be presented with the following settings to configure:

* **Enable/Disable:** Enable or disable the payment gateway.
* **Title:** The title of the payment method displayed to customers during checkout.
* **Description:** A description of the payment method displayed to customers.
* **Merchant Url:** The URL of your DV.net merchant account.
* **API Key:** Your API key for authenticating with the DV.net API.
* **API Secret:** Your API secret for validating webhook signatures.

## Running the Project 🏃‍♀️

After completing the installation and configuration steps, you can start testing the integration:

* **WordPress Site:** `http://localhost:8000` 🌐
* **WordPress Admin:** `http://localhost:8000/wp-admin` 🔑
* **Mailpit (for viewing emails):** The Docker setup includes Mailpit for intercepting and viewing emails sent by WordPress. You can access it at `http://localhost:1080`. 📧