<?php

echo "Building PHAR archive for Paraunit...\n";

$realSrc = realpath(__DIR__ . '/../src');
$copiedSrc = __DIR__ . '/build/src';

$pharFile = __DIR__ . '/paraunit.phar';
$buildDir = __DIR__ . '/build';

$realVendor = realpath(__DIR__ . '/../vendor');
$copiedVendor = __DIR__ . '/build/vendor';

// Copy files needed for build
`cp -r $realSrc $copiedSrc`;
`cp -r $realVendor $copiedVendor`;

$phar = new Phar(
    $pharFile,
    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
    "paraunit.phar"
);

$phar->buildFromDirectory($buildDir);

$phar->setStub(file_get_contents(__DIR__ . '/stub.php'));

// Remove copied files
`rm -rf $copiedSrc`;
`rm -rf $copiedVendor`;

`chmod a+x $pharFile`;

echo "Done!\n";
