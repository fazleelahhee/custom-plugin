# Custom plugin ( Inpsyde Developer Coding test)

Purpose of this plugin is Inpsyde evaluate my PHP programing skills & coding standard/ WordPress Coding standard as well as my communications skills.

## Task description

When installed, the plugin has to make available a custom endpoint on the WordPress site. With “custom endpoint” we mean an arbitrary URL not recognized by WP as a standard URL, like a permalink or so.

When a visitor navigates to that endpoint, the plugin has to send an HTTP request to a REST API endpoint. The API is available at https://jsonplaceholder.typicode.com and the endpoint to call is /users.

The plugin will parse the JSON response and will use it to build and display an HTML table.

Each row in the HTML table will show the details for a user. The column's id, name, and username are mandatory

### Features

* Admin can able to update custom endpoint from plugins settings page.
* Default template override
* Filter hook available for update ajax respose before dispatch
* Cache API response

## Installation

### Minimum requirements
* WordPress latest version
* PHP version version >= 7.3.0

### Installation via composer
For this plugin development I use composer base ["WordPress Boilerplate"](https://roots.io/bedrock/).

add to composer.json
```
  "repositories": [
    ...
    {
      "url": "https://bitbucket.org/fazleelahee/inpsyde-custom-plugin.git",
      "type": "git"
    }
  ],

  "require": {
      ....
      "inpsyde/custom-plugin": "master"
    }
```

If you use composer to pull plugin from repository, you have to make sure plugin installations path setup correctly.

here is the example:
```
   "extra": {
     "installer-paths": {
       "web/app/mu-plugins/{$name}/": ["type:wordpress-muplugin"],
       "web/app/plugins/{$name}/": ["type:wordpress-plugin"],
       "web/app/themes/{$name}/": ["type:wordpress-theme"]
     },
     "wordpress-install-dir": "web/wp"
   }
```

Once composer.json file updated, than need to run composer update command to pull plugins from the git repository.

```
    $composer update
```

When composer update complete, than goto WordPress Admin -> Plugins, then activate "Inpsyde Custom Plugin"

### Installation without composer

Clone from the repository

```
    $cd pathToWordPressRootDir/wp-contents/plugins
    $mkdir custom-plugin
    $cd custom-plugin
    $git clone https://fazleelahee@bitbucket.org/fazleelahee/inpsyde-custom-plugin.git .
```

When git clone is done, than goto the WordPress Admin -> Plugins, then activate "Inpsyde Custom Plugin"

### Differences both type of WordPress installation
Composer base WordPress installation plugin classes are loaded via composer auto loader. However, standard WordPress installation I have added SPL auto loader to load the class.


## Template Override
Default template can be override by placing "template-wpc-plugin.php" in current working theme folder. Frontend design can modify/ update without modifying file in the plugin.

## Filtering/ mutating API response

You can able to add/remove or edit table header using filter hook. Available filters:
* wpcp_plugin_user_collection
* wpcp_plugin_single_user

**wpcp_plugin_user_collection**
Using this filter you can able to add/remove column from user list table. Following example I will add "website" column user table without modifying plugin code.

**Note:** Place this code into functions.php or your custom plugin.

 ```
add_filter('wpcp_plugin_user_collection', 'modify_users_table');
function modify_users_table ($data)
{
	if(isset($data['field_display'])) {
		$data['field_display'][] = ['key' => 'website', 'label' => 'Website', 'link' => 'n'];
		return $data;
	}
}
 ```

**wpcp_plugin_single_user**
This filter allow you to modify information displaying in the frontend. For example, if you want to displaying user website link instead of the plain text.

**Note:** Place this code into functions.php or your custom plugin.

```
add_filter('wpcp_plugin_single_user', 'user_website_link');
function user_website_link ($data)
{
	if(isset($data['data']['website'])) {
		$data['data']['website'] = '<a href="'.$data['data']['website'].'">'.$data['data']['website'].'</a>';
	}
	return $data;
}
```

### Cache Api Response
You can able to cache API adding constant to your wp-config.php file.

```
/** Cache API Response **/
define('WPCPLUGIN_API_CACHE', true);
```

### Fontend and JavaScript
I use ajax request to populate user list and user details in frontend. I use webpack to compile typescript to javascript.

**Installing npm dependancies**

```
$cd pathToWordPressRoot/wp-content/plugins/custom-plugin/
$npm install
```

Compiling typescript

```
$npm run build

````

Build for production

```
$npn run production
```

### PHPUnit tests

```
$./vendor/bin/phpunit tests
PHPUnit 9.2-g1899b60ea by Sebastian Bergmann and contributors.

...........                                                       11 / 11 (100%)

Time: 00:00.332, Memory: 4.00 MB

OK (11 tests, 11 assertions)

````

### PHPCS

```
$./vendor/bin/phpcs
................... 19 / 19 (100%)

Time: 2.24 secs; Memory: 16MB
```



