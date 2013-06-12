## Pre
A handsome replacement for print\_r & var\_dump. Output debugging info in a minimally styled `<pre>` block. 

```php
// basic usage
echo Pre::r($data);
```

![Basic Example](https://github.com/paste/Pre.php/raw/master/examples/basic_example.png)  

```php
// add data to storage queue with label
Pre::add($data, 'My Debug Data');

// configure dimensions
Pre::$config['width'] = 400;
Pre::$config['height'] = 80;

// render and clear queue
echo Pre::render();
```

![Label Example](https://github.com/paste/Pre.php/raw/master/examples/label_example.png)  



Installation
------------

[Use Composer](http://getcomposer.org/). Add `paste/pre` to your project's `composer.json`:

```json
{
    "require": {
        "paste/pre": "dev-master"
    }
}
```

Or just include Pre.php directly into your project. You might also want to setup the Pre() function shortcut for convenience:

```php
<?php
// include Pre lib
require '/path/to/src/Paste/Pre.php';

// setup Pre() function shortcut
if (! function_exists('Pre')) {
	function Pre($data, $label = NULL) {
		return Paste\Pre::render($data, $label);
	}
}
```

Basic Usage
-----------

You can use Pre in two basic ways; output debugging data directly to the browser or add the data to a queue and display it later in execution.

Optional:
 - add a label to identify the data
 - configure the height/width of the `<pre>` block

#### Direct Output
```php
<?php
// using function shorcut
echo Pre($data);

// using shortcut r() method with label
echo Pre::r($data, 'Debug Label');

// using regular render() method, no label
echo Pre::render($data);
```

#### Data Queue
```php
<?php
// add data to queue with a label
Pre::add($data1, 'Debug Data #1');

// ...do some stuff...
Pre::add($data2, 'Debug Data #2');

// later... display the data
echo Pre::render();
```

vs. print\_r() and var\_dump()
----------
![Comparison](https://github.com/paste/Pre.php/raw/master/examples/pre_comparison.png)  

