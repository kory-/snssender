#Amazon Sns for Laravel

## Install

```
composer require socialgrid/snssender
```

config/app.php
providers
```
Socialgrid\SnsSender\ServiceProvider::class,
```

config/app.php
aliases
```
'SnsSender' => Socialgrid\SnsSender\Facade::class,
```

php artisan vendor:publish --provider="Socialgrid\SnsSender\ServiceProvider"

## Usage
