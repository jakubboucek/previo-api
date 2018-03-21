# Previo API pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    previo: NAttreid\PrevioApi\DI\PrevioApiExtension

smartEmailing:
    login: 'username@mail.com'
    password: 'password'
    hotelId: 3
    debug: true # default false
```

Použití

```php
/** @var NAttreid\PrevioApi\PrevioClient @inject */
public $previo;

```
