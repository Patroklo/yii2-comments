Comments module for Yii2 
========================

This module provide a comments managing system for Yii2 application and it's based on the [yii2mod/comments](https://github.com/yii2mod/yii2-comments) package by Igor Chepurnoy.

# Installation


#### 1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist cyneek/yii2-comments "*"
```

or add to the require section of your composer.json

```json
"cyneek/yii2-comments": "*"
```


#### 2. Run migrations:
  
  ```php
      php yii migrate --migrationPath=@vendor/cyneek/yii2-comments/migrations
  ```

#### 3. Add module data to Yii2 config file:

```php
    'modules' => [
        'comment' => [
            'class' => 'cyneek\comments\Module',
            'userIdentityClass' => 'user\Identity'
        ],
     ...
     ]
```

#### 4. (Optional step) Manage comments in admin panel: 

Add following code to admin module section in main config

```php
  'modules' => [
    'comment' => [
        'class' => 'cyneek\comments\Module',
        'userIdentityClass' => 'user\Identity',
          'controllerMap' => [
                'comments' => 'cyneek\comments\controllers\ManageController'
          ]  
    ],
 ...
 ]

```

Then you will be able to access the management section through the following URL:
  
  ```
    http://localhost/path/to/index.php?r=admin/comments/index
  ```


# Usage


- Use in view with ActiveRecord model:

```php
<?php echo \cyneek\comments\widgets\Comment::widget(['model' => $model]); ?>
```

- Use in view with entity and entityId strings

```php
<?php echo \cyneek\comments\widgets\Comment::widget(['entity' => '483f0e5a', 'entityId' => 149]); ?>
```

# Parameters
 
### Module parameters:

Here is a list of the parameters that the module will accept in the application config file:

* **userIdentityClass** (optional, string) The user identity class that Yii2 uses to provide identity information about the users in the App. If not defined, will try to get it from the Yii2 system.

* **controllerNamespace** (optional, string) Defined namespace that module controllers will have.

* **modelMap** (optional, string[]) Classes that will be used in the module instead of the default ones. Must have a key => classname format. e.g. `'Comment' => '@app\comments\CommentModel'`. The only valid class keys are: ['Comment', 'CommentQuery', 'CommentSearch'].

* **useRbac** (optional, boolean) Default: FALSE. If it's set to TRUE, the module will user Rbac security role system to check for permissions when trying to update, create or delete comments.  

* **assetMap** (optional, string[]) Configuration that the module will use as assets instead of the default ones. In order to be able to work, must have this format:

```php
[
    'js' => ['file1'],
    'css' => ['file2'],
    'sourcePath' => 'url',
    'depends' => ['file3', 'file4']
]
```

### Widget parameters:

To be able to distinguish between the different types of comments held in your application, the system will add to each one an entity and entityId. This values will be used to filter through all the comments in the system to load only the required ones in each view.  

For that you'll have to use one of this options:

* **model** (optional, object) Loaded ActiveRecord object that will be used to extract the entity and entity id of the comment widget.

or

* **entity** (optional, string) String that defines a manual comment entity in case we don't need to use a loaded ActiveRecord object.

* **entityId** (optional, integer)


There's also a list of optional parameters to define the behavior of the comment system:


* **maxLevel** (optional, integer) Default 7. Maximum nesting level of answers allowed in your comment system. 

* **entityIdAttribute** (optional, string) Only applicable if we are loading the widgets with the `model` parameter. Will stablish the field name that holds the comment entityId parameter.

* **clientOptions** (optional, array) Array that holds javascript asset parameters. Check the `comment-list.js` asset file to get the full list of possible variables.
 
* **pjax** (optional, boolean) Default FALSE. If it's TRUE, the comment system will use ajax to send and reload comment data to the server.

* **showDeletedComments** (optional, boolean) Default TRUE. If set as FALSE will hide all the deleted comments. Warning: if the comment system is set as bested and this parameter as TRUE, will also hide all the answers of every deleted comment.

* **nestedBehavior** (optional, boolean) Default TRUE. If set as FALSE will show all comments in the same level instead of nesting the answers of each comment.

* **allowAnonymousComments** (optional, boolean) Default TRUE. Hides the new comment form if set as False when not logged in the application.

* **pagination** (optional, array) Sets the pagination options of the comment listView. For more info about pagination options: [HERE](http://www.yiiframework.com/doc-2.0/yii-data-pagination.html)

* **sort** (optional, array) Sorts the comments in the comment listView. For more info about sorting items in Yii2: [HERE](http://www.yiiframework.com/doc-2.0/yii-data-sort.html)


## Extending the module:


#### Extending Model files

Depending on which ones you need, you can set the `modelMap` config property:

```php
	// ...
	'modules' => [
		// ...
		'comment' => [
		   'class' => 'cyneek\comments\Module',
		    'modelMap' => [
		        'Comment' => '@app\comments\CommentModel'
		    ]
		]
	],
	// ...
```


Attention: keep in mind that if you are changing the `Comment` model, the new class should always extend the package's original `Comment` class.


#### Attaching behaviors and event handlers
 
The package allows you to attach behavior or event handler to any model. To do this you can set model map like so:

```php
	// ...
	'modules' => [
		// ...
		'comments' => [
		    'class' => 'cyneek\comments\Module',
		    'userIdentityClass' => 'app\models\User',
		    'modelMap' => [
		        'Comment' => [
		            'class' => '@app\comments\CommentModel',
		            'on event' => function(){
		                // code here
		            },
		            'as behavior' => 
		                ['class' => 'Foo'],
		    ]
		]
	],
	// ...
```

#### Extending View files

You can extend the view files supplied by this package using the `theme` component in the config file.

```php
// app/config/web.php

'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@vendor/cyneek/comments/widgets/views' => '@app/views/comments', // example: @app/views/comment/_form.php
            ],
        ],
    ],
],

```