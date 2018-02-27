Yii Error Handler with Whoops
=============================

Integrates the Whoops library into Yii 1.1.

Usage
-----

1. Install it:
    - Using [Composer] (it will automatically install Whoops main libraries as well):
    ```shell
    composer require tianye/yii-whoops ~1.0
    composer install
    ```
    - Or [downloading] and unpacking it in your `extensions` folder.

2. If you're using Composer, I strongly recomend you to create a `vendor` alias if you haven't yet.
   Add this to the beginning of your `config/main.php`:

    ```php
    Yii::setPathOfAlias('vendor', __DIR__.'/../../vendor');
    ```

3. Replace your `errorHandler` entry at `config/main.php` with the error handler class. Example:

    ```php
    'errorHandler' => ['class' => 'vendor.tianye.yii-whoops.WhoopsErrorHandler']
    ```