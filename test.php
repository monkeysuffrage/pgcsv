<?
require_once dirname(__FILE__) . '/pgcsv.php';

$fields = array('name', 'phone', 'address');

$csv = new CSV('output.csv', $fields);

$person = array(
  'name' => 'Joe Brown',
  'phone' => '123 222-0123',
  'address' => '666 Buckingham Palace'
);

$csv->save($person);
$csv->close();

$data = CSV::read('output.csv');
var_dump($data);

foreach(CSV::iterate('output.csv') as $row){
  echo $row['name'] . "\n";
}
?>