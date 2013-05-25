## Pre
A handsome replacement for print\_r & var\_dump.

![Label Example](https://github.com/paste/Pre.php/raw/master/label_example.png)  


Installation
------------

[Use Composer](http://getcomposer.org/). Add `paste/pre` to your project's `composer.json`:

```json
{
    "require": {
        "paste/pre": ">=1.0"
    }
}
```

Or just include Pre.php directly into your project. You might also want to setup the Pre() function shortcut:

```php
<?php
// include Pre lib
require '/path/to/src/Paste/Pre.php';
use Paste\Pre;

// setup Pre() function shortcut
if (! function_exists('Pre')) {
	function Pre($data, $label = NULL) {
		return Pre::render($data, $label);
	}
}
```

Basic Usage
-----------

You can use Pre in two basic ways. You can output debugging data directly to the browser, or you can add the data to a queue and display it later.
Either way, you have the option of adding a label to identify the data.

#### Direct Output
```php
<?php
// using function shorcut
echo Pre($data);

// using shortcut method with label
echo Pre::r($data, 'Debug Label');

// using regular library method, no label
echo Pre::render($data);
```

#### Data Queue
```php
<?php
// add data to queue with a label
Pre::add($data1, 'Debug Data #1');
Pre::add($data2, 'Debug Data #2');
Pre::add($_SERVER); // add some server info

// later... display the data, or log/email/etc...
echo Pre::render();
```

Comparison
----------
![Comparison](https://github.com/paste/Pre.php/raw/master/pre_comparison.png)  

