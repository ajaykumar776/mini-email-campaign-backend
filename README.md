# Mini Email Campaign System

## About This Project
This is a Mini Email Campaign system designed to manage email campaigns efficiently. Users can create and manage campaigns, upload email lists, and perform various actions related to email marketing.

## Run This Project

### Clone It
```bash
git clone git@github.com:ajaykumar776/mini-email-campaign-backend.git

```

2. Install dependencies:
    ```bash
    composer install
    ```

3. Set up environment variables:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3. Set up environment Email Configuration in .env:
    ```bash
    .env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=
    MAIL_PASSWORD=''
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=ajaykrdtg5@gmail.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```

4. Run migrations:
    ```bash
    php artisan migrate
    ```

5. Set up Queue in Backend.
    ```bash
    php artisan queue:work
