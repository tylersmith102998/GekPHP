<?php

$directory = $_SERVER['DOCUMENT_ROOT'];

// Will exclude everything under these directories
$exclude = array('dev_util', '.git');

/**
 * @param SplFileInfo $file
 * @param mixed $key
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

$count = 0;

foreach($objects as $name => $object) {
    //echo $name;

    if (is_dir($name)) {
        //echo "yes\n";
    } else {
        $count += countLines($name);
    }
}

echo number_format($count);

function countLines($filename) {
    $lines = file($filename);

    foreach ($lines as $key => $line) {
        $line = trim($line);

        if ($line != "") {
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
