<p>
<a href="https://packagist.org/packages/lechihuy/laratrans"><img src="https://img.shields.io/packagist/dt/lechihuy/laratrans" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/lechihuy/laratrans"><img src="https://img.shields.io/packagist/v/lechihuy/laratrans" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/lechihuy/laratrans"><img src="https://img.shields.io/packagist/l/lechihuy/laratrans" alt="License"></a>
</p>

# Laratrans
Support multiple language resources for Laravel.

## Docs
### Installation
```shell
composer require lechihuy/laratrans
```

After you install the package successfully, open your terminal at your project and run the following command:
```shell
php artisan laratrans:install
```

### Configuration
You can configure the package in `app/config/translatable.php`, please read it to detail more.

### Database
...

### Model
Imaginary, you need to apply translation for `Post` model.

First, use the `Laratrans\Translatable` trait in `Post` model file as bellow:
```php
<?php

use Laratrans\Translatable;

class Post extends Model
{
    use Translatable;
}
```

Next, open your terminal and run the following command to create translation model:
```shell
php artisan make:model Translation/PostTranslation
```

The `Translation` namespace must be corresponding with `translation_namespace` in the configuration file. Besides, the suffix of translation model name must be `Translation`.

`Post` model need to translate `title`, `description` columns. You simple declare the `translatedAttributes` property in this model.
```php
class Post extends Model
{
    /**
     * The columns are translatable.
     * 
     * @var string[]
    */
    public array $translatedAttributes = ['title', 'description'];
    
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['translations'];
}
```

You need to declare eager loading to it work. The package that offers two eager loadings are `translations` and `translation`.

If you serialize a translatable model with `translations` eager loading, the response can be given as bellow:
```json
{
    "id": 1,
    "created_at": "2021-10-07T06:55:46.000000Z",
    "updated_at": "2021-10-07T06:55:46.000000Z",
    "title": "Xin chào thế giới",
    "description": "Đây là bài viết đầu tiên",
    "translations": [
        {
            "post_id": 1,
            "locale": "vi",
            "title": "Xin chào thế giới",
            "description": "Đây là bài viết đầu tiên"
        },
        {
            "post_id": 1,
            "locale": "en",
            "title": "Hello world",
            "description": "This is the first post"
        }
    ]
}
```

Otherwise, if you use `translation` eager loading, the response won't contain `translations` property.

You also hidden a few redundant properties if you want via `$hidden` property in the model.
```php
class Post extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['post_id'];
}
```

## License

Laratrans is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
