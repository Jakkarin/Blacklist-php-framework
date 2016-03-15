<?php
date_default_timezone_set('Asia/bangkok');
echo ($a = 'a');
// touch('a');
// var_dump(date('H:i:s',filemtime('a')));
// var_dump(date('H:i:s',filemtime('a') + 3600));

// $globals['a'] = (object) array('a' => 'a');
// $a = clone $globals['a'];
// echo "$a->a <br/>";
// $globals['a']->a = 'b';
// echo "$a->a <br/>";


// $array = array(
//     'a' =>  'a',
//     'b' =>  'b',
//     'c' =>  'c'
// );
//
// $loop = 50;
// $s = microtime(true);
// for($i = 0;$i < $loop; $i++) {
//
// }
// $e = microtime(true);
// var_dump('First : ', ($e - $s));
//
// $s = microtime(true);
// for($i = 0;$i < $loop; $i++) {
//
// }
// $e = microtime(true);
// var_dump('Second : ', ($e - $s));

// class asd
// {
//     public function asd()
//     {
//
//     }
//
//     public static function asds()
//     {
//
//     }
// }
//
// $loop = 50000;
// $s = microtime(true);
// $m1 = memory_get_usage();
// for($i = 0;$i < $loop; $i++) {
//     $asd = new asd;
//     $asd->asd();
// }
// $e = microtime(true);
// var_dump('First : ', ($e - $s),(memory_get_usage() - $m1));
//
// $s = microtime(true);
// $m1 = memory_get_usage();
// for($i = 0;$i < $loop; $i++) {
//     asd::asds();
// }
// $e = microtime(true);
// var_dump('Second : ', ($e - $s),(memory_get_usage() - $m1));
