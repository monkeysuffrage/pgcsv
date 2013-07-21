pgcsv
=====

A 'pretty good' csv class


### Writing
-----------

```php
$fields = array('name', 'phone', 'address');

$csv = new CSV('output.csv', $fields);

$person = array(
  'name' => 'Jow Brown'
  'phone' => '123 222-0123'
  'address' => '666 Buckingham Palace'
);

$csv->save($person);
```

By default, CSV saves data with a utf-8 BOM to make it Excel-friendly. You can change that though.

### Reading (iterating anyway)
-----------

```php
foreach(CSV::iterate('input.csv') as $row){
  echo $row['name'] . "\n";
}
```

CSV expects the first row to be headers, but you can change that. You can also change the delimiter.