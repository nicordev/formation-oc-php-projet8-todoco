<?php

function requireFromRoot(array $files)
{
    $root = __DIR__ . "/..";

    foreach ($files as $file) {
        require "$root/$file";
    }
}

function requireFolderFromRoot(string $folder)
{
    requireFromRoot(glob("$folder/*.php"));
}

requireFromRoot([
    "features/bootstrap/doctrineHandler.php",
    "vendor/autoload.php"
]);
requireFolderFromRoot("vendor\\symfony\\validator\\Constraints");

$doctrineHandler = new DoctrineHandler();

$doctrineHandler->createSchema(["App\\Entity\\Task", "App\\Entity\\User"]);

echo "Done.";