<?php
// The
$a = [
    'a',
    'the',
    'one',
    'some'
];

// NOUN
$b = [
    'cat',
    'dog',
    'pizza',
    'monster',
    'flesh',
    'zombie',
    'universe',
    'tit',
    'pair of headphones',
    'hobo',
    'wizard',
    'student',
    'crappy teacher',
    'Sevin Heins',
    'Danny Mandina',
    'Donald Lump',
    'Hitlery Clit-On',
    'Sam Keedwell',
    'Tyler Smith'
];

// VERB
$c = [
    'ran',
    'jumped',
    'crapped',
    'teleported',
    'escaped',
    'humped',
    'raped',
    'farted',
    'pooped',
    'said "oh la la"',
    'gagged'
];

// over
$d = [
    'under',
    'over',
    'with',
    'without',
    'to the side of',
    'on top of',
    'on'
];

$adj = [
    'rather large',
    'rather small',
    'extremely big',
    'long',
    'pointy',
    'chaffed'
];

for ($i = 0; $i < 100; $i++) {

    $arand = $a[rand(0, count($a) - 1)];
    $brand = $b[rand(0, count($b) - 1)];
    $crand = $c[rand(0, count($c) - 1)];
    $drand = $d[rand(0, count($d) - 1)];
    $erand = $a[rand(0, count($a) - 1)];
    $adj1 = $adj[rand(0, count($adj) - 1)];
    $adj2 = $adj[rand(0, count($adj) - 1)];
    $frand = $b[rand(0, count($b) - 1)];

    $sentence = ucfirst("$arand $adj1 $brand $crand $drand $erand $adj2 $frand.");

    echo "<h1 style=\"font-family: Tahoma;\">" . $sentence . "</h1><br />";

}
