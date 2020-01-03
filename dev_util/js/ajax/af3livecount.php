<?php

$directory = $_SERVER['DOCUMENT_ROOT'];

//$directory = '../admin';

// Will exclude everything under these directories
$exclude = array('dev_util', '.git');

/**
 * @param SplFileInfo $file
 * @param mixed $key
 *
 * @param RecursiveCallbackFilterIterator $iterator
 * @return bool True if you need to recurse or if the item is acceptable
 */
$filter = function ($file, $key, $iterator) use ($exclude) {
    if ($iterator->hasChildren() && !in_array($file->getFilename(), $exclude)) {
        return true;
    }
    return $file->isFile();
};

$innerIterator = new RecursiveDirectoryIterator(
    $directory,
    RecursiveDirectoryIterator::SKIP_DOTS
);
$objects = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator($innerIterator, $filter)
);



//$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
//foreach($objects as $name => $object){
//    echo "$name\n";
//}

$count = 0;

foreach($objects as $name => $object) {
    //echo $name;

    $name = str_replace('\\', '/', $name);
    $dir = $_SERVER['DOCUMENT_ROOT'] . '/util';
    //echo $dir;



    if (is_dir($name)) {
        //echo "<pre>" . $name . "<br/>";
    } else {
        $count += countLines($name);
    }
}

echo number_format($count);

function countLines($filename) {
    $lines = file($filename);

    foreach ($lines as $key => $line) {
        $line = trim($line);

        if ($line == "") {
            unset($lines[$key]);
            continue;
        }

        if ((substr($line, 0, 1) == "/") || (substr($line, 0, 1) == "*")) {
            unset($lines[$key]);
            continue;
        }

        if ($line == "<?php" || $line == "?>") {
            unset($lines[$key]);
            continue;
        }
    }

    $lines = array_values($lines);

    //echo '<pre>';
    //print_r($lines);

    return count($lines);
}
