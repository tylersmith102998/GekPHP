<pre>
<?php

define("DS", DIRECTORY_SEPARATOR);

chdir("../");

$cwd = getcwd();

if (isset($_GET['directory']) && !empty($_GET['directory'])) {

    echo $dir = $cwd . DS . $_GET['directory'];
    echo "\n";

    $files = scandir($dir);

    //echo scanAllFiles($dir);

    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    //foreach($objects as $name => $object){
    //    echo "$name\n";
    //}

    $count = 0;

    foreach($objects as $name => $object) {
        //echo $name;

        if (is_dir($name)) {
            //echo "yes\n";
        } else {
            $count += countLines($name);
        }
    }

    echo $count . " lines of code...";

}

function scanAllFiles($path) {
    if ($handle = opendir($path)) {
        echo "Directory Handle: {$handle}\n";
        echo "Entries:\n";

        while (false !== ($entry = readdir($handle))) {
            echo "$entry\n";
        }

        closedir($handle);
    }
}

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

/*
function scanAllFiles($path, $files) {
    foreach ($files as $file) {
        if (($file == ".") || ($file == "..")) {

        } else {
            if (is_dir($file)) {
                echo 'huh';
                $newDir = $path . "/{$file}";
                $newFiles = scandir($newdir);
                echo scanAllFiles($newDir, $newFiles);
            } else {
                print_r(file($path . DS . $file));
            }
        }
    }
}*/

?>
</pre>

<form action="count.php" method="get">
    <input type="text" name="directory" placeholder="directory name" />
    <input type="submit" value="Go" />
</form>
