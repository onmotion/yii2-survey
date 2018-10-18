
```
'<module:\w+>/<controller:\w+>/<action:(\w|-)+>' => '<module>/<controller>/<action>',
'<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
```

```sh
php yii migrate --migrationPath=@vendor/onmotion/yii2-survey/migrations
```