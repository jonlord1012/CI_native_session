# CI_native_session
Compatible Session to avoid randomly reset session 


Install
---------------------

- Put 'Native_Session.php' into CI's libraries folder:
    application >> libraries >> Native_Session.php
- In application >> config >> autoload.php, put 'native_session' in libraries array


$autoload['libraries'] = array('database', 'native_session', 'user_agent');


- Set the sess_cookie_name and sess_expiration in: application >> config >> config.php

...

$config['sess_cookie_name']		= 'mydomain';
$config['sess_expiration']		= 86200;
```

- And now, just use like CI Session, well it is CI Session indeed .. :p
	I just kind of modified it ... :)




Instructions
---------------------

### Set a value

```php
$this->native_session->set_userdata('key', 'value');

// or an array

$data = array(
  'key1' => 'value1',
  'key2' => 'value2',
);

$this->native_session->set_userdata($data);
```


### Get a value

```php
$value = $this->native_session->userdata('key');
```



Flashdata Instructions
---------------------

### Set a value

```php
$this->native_session->set_flashdata('key', 'value');

// or an array

$data = array(
  'key1' => 'value1',
  'key2' => 'value2',
);

$this->native_session->set_flashdata($data);
```

### Keep Flashdata

```php
$this->native_session->keep_flashdata();
```


Destroy Session Instructions
---------------------


### Destroy Session

```php
$this->native_session->destroy();

or 

$this->native_session->sess_destroy();


```



open issues or direct contact : j.a.pambudi@gmail.com
