ElFinder Расширение для Yii 2
===========================

ElFinder — файловый менеджер для сайта.

## Поддерживаемые хранилища

mihaildev/yii2-elfinder-flysystem - https://github.com/MihailDev/yii2-elfinder-flysystem/

```
    Local
    Azure
    AWS S3 V2
    AWS S3 V3
    Copy.com
    Dropbox
    FTP
    GridFS
    Memory
    Null / Test
    Rackspace
    ReplicateAdapter
    SFTP
    WebDAV
    PHPCR
    ZipArchive
```


## Установка

Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

Либо запустить

```
php composer.phar require --prefer-dist aik27/yii2-elfinder "*"
```

или добавить

```json
"aik27/yii2-elfinder": "*"
```

в разделе `require` вашего composer.json файла.

## Настройка

```php
'controllerMap' => [
        'elfinder' => [
            'class' => 'aik27\elfinder\Controller',
            'access' => ['@'], //глобальный доступ к фаил менеджеру @ - для авторизорованных , ? - для гостей , чтоб открыть всем ['@', '?']
            'disabledCommands' => ['netmount'], //отключение ненужных команд https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#commands
            'roots' => [
                [
                    'baseUrl'=>'@web',
                    'basePath'=>'@webroot',
                    'path' => 'files/global',
                    'name' => 'Global'
                ],
                [
                    'class' => 'aik27\elfinder\volume\UserPath',
                    'path'  => 'files/user_{id}',
                    'name'  => 'My Documents'
                ],
                [
                    'path' => 'files/some',
                    'name' => ['category' => 'my','message' => 'Some Name'] //перевод Yii::t($category, $message)
                ],
                [
                    'path'   => 'files/some',
                    'name'   => ['category' => 'my','message' => 'Some Name'], // Yii::t($category, $message)
                    'access' => ['read' => '*', 'write' => 'UserFilesAccess'] // * - для всех, иначе проверка доступа в даааном примере все могут видет а редактировать могут пользователи только с правами UserFilesAccess
                ]
            ],
            'watermark' => [
            		'source'         => __DIR__.'/logo.png', // Path to Water mark image
                     'marginRight'    => 5,          // Margin right pixel
                     'marginBottom'   => 5,          // Margin bottom pixel
                     'quality'        => 95,         // JPEG image save quality
                     'transparency'   => 70,         // Water mark image transparency ( other than PNG )
                     'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
                     'targetMinPixel' => 200         // Target image minimum pixel size
            ]
        ]
    ],
```

```php
'controllerMap' => [
        'elfinder' => [
			'class' => 'aik27\elfinder\PathController',
			'access' => ['@'],
			'root' => [
				'path' => 'files',
				'name' => 'Files'
			],
			'watermark' => [
						'source'         => __DIR__.'/logo.png', // Path to Water mark image
						 'marginRight'    => 5,          // Margin right pixel
						 'marginBottom'   => 5,          // Margin bottom pixel
						 'quality'        => 95,         // JPEG image save quality
						 'transparency'   => 70,         // Water mark image transparency ( other than PNG )
						 'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
						 'targetMinPixel' => 200         // Target image minimum pixel size
			]
		]
    ],
```

Разница между PathController и Controller в том что PathController работает только с одной папкой также имеет доп возможность передать в запросе на открытие под деритории


На данный момент реализованно использование только LocalFileSystem хранилища (aik27\elfinder\volume\Local и aik27\elfinder\volume\UserPath)
для использования остальных вам прийдётся всё настраивать через aik27\elfinder\volume\Base
также добавленно расширение  https://github.com/MihailDev/yii2-elfinder-flysystem/ это дополнение позволяет интегрировать Flysystem хранилища такие как
    Local
    Azure
    AWS S3 V2
    AWS S3 V3
    Copy.com
    Dropbox
    FTP
    GridFS
    Memory
    Null / Test
    Rackspace
    ReplicateAdapter
    SFTP
    WebDAV
    PHPCR
    ZipArchive
    
## Настройка callback-ов для событий 
```php
'controllerMap' => [
        'elfinder' => [
            ...            
            'managerOptions' => [
                ...
                'handlers' => [
                    'select' => 'function(event, elfinderInstance) {
                                    console.log(event.data);
                                    console.log(event.data.selected);
                                }', 
                    'open' => 'function(event, elfinderInstance) {...}',
                ],
                ...
            ],
            ...
        ]
    ],
```

список событий  - https://github.com/Studio-42/elFinder/wiki/Client-event-API#event-list

## Настройка Плагинов
Изза сложной настройки была переделанна работа плагинов но возможность использовать старые плагины присутствует
```php
'controllerMap' => [
        'elfinder' => [
            'class' => 'aik27\elfinder\Controller',
            //'plugin' => ['\aik27\elfinder\plugin\Sluggable'],
            'plugin' => [
                [
                    'class'=>'\aik27\elfinder\plugin\Sluggable',
                    'lowercase' => true,
                    'replacement' => '-'
                ]
             ],
             'roots' => [
                             [
                                 'baseUrl'=>'@web',
                                 'basePath'=>'@webroot',
                                 'path' => 'files/global',
                                 'name' => 'Global',
                                 'plugin' => [
                                        'Sluggable' => [
                                            'lowercase' => false,
                                        ]
                                 ]
                             ],
                         ]

```

Настройка старого плагина (на примере плагина Sanitizer)
```php
'controllerMap' => [
        'elfinder' => [
            'class' => 'aik27\elfinder\Controller',
            'connectOptions' => [
                'bind' => [
                    'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array(
                        'Plugin.Sanitizer.cmdPreprocess'
                    ),
                    'ls' => array(
                        'Plugin.Sanitizer.cmdPostprocess'
                    ),
                    'upload.presave' => array(
                        'Plugin.Sanitizer.onUpLoadPreSave'
                    )
                ],
                'plugin' => [
                    'Sanitizer' => array(
                        'enable' => true,
                        'targets'  => array('\\','/',':','*','?','"','<','>','|'), // target chars
                        'replace'  => '_'    // replace to this
                    )
                ],
            ],


             'roots' => [
                             [
                                 'baseUrl'=>'@web',
                                 'basePath'=>'@webroot',
                                 'path' => 'files/global',
                                 'name' => 'Global',
                                 'plugin' => [
                                        'Sanitizer' => array(
                                                                'enable' => true,
                                                                'targets'  => array('\\','/',':','*','?','"','<','>','|'), // target chars
                                                                'replace'  => '_'    // replace to this
                                                            )
                                 ]
                             ],
                         ]

```

## Использование

```php
use aik27\elfinder\InputFile;
use aik27\elfinder\ElFinder;
use yii\web\JsExpression;

echo InputFile::widget([
    'language'   => 'ru',
    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter'     => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'name'       => 'myinput',
    'value'      => '',
]);

echo $form->field($model, 'attribute')->widget(InputFile::className(), [
    'language'      => 'ru',
    'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
    'options'       => ['class' => 'form-control'],
    'buttonOptions' => ['class' => 'btn btn-default'],
    'multiple'      => false       // возможность выбора нескольких файлов
]);

echo ElFinder::widget([
    'language'         => 'ru',
    'controller'       => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter'           => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'callbackFunction' => new JsExpression('function(file, id){}') // id - id виджета
]);

```

## Использование при работе с PathController
```php
use aik27\elfinder\InputFile;
use aik27\elfinder\ElFinder;
use yii\web\JsExpression;

echo InputFile::widget([
    'language'   => 'ru',
    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории  
    'filter'     => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'name'       => 'myinput',
    'value'      => '',
]);

echo $form->field($model, 'attribute')->widget(InputFile::className(), [
    'language'      => 'ru',
    'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории 
    'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
    'options'       => ['class' => 'form-control'],
    'buttonOptions' => ['class' => 'btn btn-default'],
    'multiple'      => false       // возможность выбора нескольких файлов
]);

echo ElFinder::widget([
    'language'         => 'ru',
    'controller'       => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории 
    'filter'           => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'callbackFunction' => new JsExpression('function(file, id){}') // id - id виджета
]);

```

## CKEditor
```php
use aik27\elfinder\ElFinder;

$ckeditorOptions = ElFinder::ckeditorOptions($controller,[/* Some CKEditor Options */]);

```

Для указания подкаталога (при использовании PathController)
```php
use aik27\elfinder\ElFinder;

$ckeditorOptions = ElFinder::ckeditorOptions([$controller, 'path' => 'some/sub/path'],[/* Some CKEditor Options */]);

```

Использование совместно с приложением "mihaildev/yii2-ckeditor" (https://github.com/MihailDev/yii2-ckeditor)

```php
use aik27\ckeditor\CKEditor;
use aik27\elfinder\ElFinder;

$form->field($model, 'attribute')->widget(CKEditor::className(), [
  ...
  'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
  ...
]);
```

Для указания подкаталога (при использовании PathController)

```php
use aik27\ckeditor\CKEditor;
use aik27\elfinder\ElFinder;

$form->field($model, 'attribute')->widget(CKEditor::className(), [
  ...
  'editorOptions' => ElFinder::ckeditorOptions(['elfinder', 'path' => 'some/sub/path'],[/* Some CKEditor Options */]),
  ...
]);
```

## Проблемы
При встраивание без iframe возможен конфликт с bootstrap.js. Studio-42/elFinder#740
Решение - добавляем в шаблон запись
```php

aik27\elfinder\Assets::noConflict($this);

```

## Полезные ссылки

ElFinder Wiki - https://github.com/Studio-42/elFinder/wiki

Flysystem

https://github.com/MihailDev/yii2-elfinder-flysystem/

https://github.com/barryvdh/elfinder-flysystem-driver

https://github.com/creocoder/yii2-flysystem

http://flysystem.thephpleague.com/




