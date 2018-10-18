
Survey module for Yii2 application
--

[![Latest Stable Version](https://poser.pugx.org/onmotion/yii2-survey/v/stable)](https://packagist.org/packages/onmotion/yii2-survey)
[![Total Downloads](https://poser.pugx.org/onmotion/yii2-survey/downloads)](https://packagist.org/packages/onmotion/yii2-survey)
[![Monthly Downloads](https://poser.pugx.org/onmotion/yii2-survey/d/monthly)](https://packagist.org/packages/onmotion/yii2-survey)
[![License](https://poser.pugx.org/onmotion/yii2-survey/license)](https://packagist.org/packages/onmotion/yii2-survey)


>! Note: the module under active developing, so it may have vary errors and unstable work.
> Highly appreciate your PR.

![fluent](https://github.com/onmotion/yii2-survey/blob/docs/examples/front-short.png?raw=true)


Installation
--

* Just run:

    composer require onmotion/yii2-survey

or add 

    "onmotion/yii2-survey": "*"

to the require section of your composer.json file.

* apply migration:


```sh
php yii migrate --migrationPath=@vendor/onmotion/yii2-survey/migrations
```

* Define module to your config:

```php
'modules' => [
//...
    'survey' => [
        'class' => '\onmotion\survey\Module',
        'params' => [
            'uploadsUrl' => 'http://advanced-frontend.lh/uploads/survey/', // full URL of the folder where the images will be uploaded.
           // 'uploadsUrl' => '/uploads/survey/', // or for basic
            'uploadsPath' => '@frontend/web/uploads/survey/', // absolute path to the folder where images will be saved.
        ],
//            'as access' => [
//                'class' => AccessControl::class,
//                'except' => ['default/done'],
//                'only' => ['default*'],
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['survey'],
//                    ],
//                ],
//            ],
    ],
//...
]
```

don't forget change your own params.

Usage
--

If you are using the Yii basic template, you must manually define `$controllerNamespace` for module.

*onmotion\survey\controllers* - backend (admin/create/edit surveys)

*onmotion\survey\widgetControllers* - default (for widget)

Now go to `/survey` in your backend and create a survey.

![fluent](https://github.com/onmotion/yii2-survey/blob/docs/examples/back-list.png?raw=true)

After that you can select Survey entities and show it for user, for example:

```php
echo \onmotion\survey\Survey::widget([
   'surveyId' => 1
]);
```

![fluent](https://github.com/onmotion/yii2-survey/blob/docs/examples/front.png?raw=true)

Admin:

![fluent](https://github.com/onmotion/yii2-survey/blob/docs/examples/back-create.png?raw=true)

![fluent](https://github.com/onmotion/yii2-survey/blob/docs/examples/back-review.png?raw=true)