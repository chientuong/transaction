<?php

// Re-doing the separation securely
$basePath = __DIR__;
$appDir = $basePath . '/app';
$sourceDir = $basePath . '/source';

// 1. Move Domain to source
if (is_dir($appDir . '/Domain')) {
    if (!is_dir($sourceDir)) mkdir($sourceDir);
    rename($appDir . '/Domain', $sourceDir . '/Domain');
}

// 2. Extract Filament components to app/Filament
if (!is_dir($appDir . '/Filament')) mkdir($appDir . '/Filament');

$filaments = glob($sourceDir . '/Domain/*/Presentation/Filament', GLOB_ONLYDIR);
foreach ($filaments as $filamentDir) {
    // Expected structure: .../Filament/Resources, .../Filament/Pages, etc
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($filamentDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            $relPath = substr($item->getPathname(), strlen($filamentDir) + 1);
            $targetDir = $appDir . '/Filament/' . $relPath;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
        } else {
            $relPath = substr($item->getPathname(), strlen($filamentDir) + 1);
            $targetFile = $appDir . '/Filament/' . $relPath;
            rename($item->getPathname(), $targetFile);
        }
    }
    
    // Clean up empty directories
    shell_exec("rm -rf " . escapeshellarg($filamentDir));
}

// 3. Update namespaces in all files
function recursivelyUpdateNamespaces($dir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getExtension() === 'php') {
            $content = file_get_contents($item->getPathname());
            $original = $content;
            
            // Adjust Filament namespaces inside app/Filament
            if (str_contains($item->getPathname(), '/app/Filament/')) {
                // Remove the Domain/*/Presentation part from namespaces
                // Example: namespace App\Domain\Transaction\Presentation\Filament\Resources;
                // -> namespace App\Filament\Resources;
                $content = preg_replace(
                    '/namespace App\\\\Domain\\\\[A-Za-z0-9]+\\\\Presentation\\\\Filament(.*?);/',
                    'namespace App\Filament$1;',
                    $content
                );
            } else if (str_contains($item->getPathname(), '/source/Domain/')) {
                // Change App\Domain to Source\Domain
                $content = preg_replace(
                    '/namespace App\\\\Domain(.*?);/',
                    'namespace Source\Domain$1;',
                    $content
                );
            }

            // Global string replacements
            $content = str_replace('App\Domain', 'Source\Domain', $content);
            // Fix any references to Filament Resources that got changed to Source\Domain
            $content = preg_replace(
                '/Source\\\\Domain\\\\[A-Za-z0-9]+\\\\Presentation\\\\Filament/',
                'App\Filament',
                $content
            );

            if ($content !== $original) {
                file_put_contents($item->getPathname(), $content);
                echo "Updated: " . $item->getPathname() . "\n";
            }
        }
    }
}

// Update tests correctly across all files
function fixTestNamespacesAndUses($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getExtension() === 'php') {
            $content = file_get_contents($item->getPathname());
            $original = $content;
            
            $content = str_replace('App\Domain', 'Source\Domain', $content);
            $content = preg_replace(
                '/Source\\\\Domain\\\\[A-Za-z0-9]+\\\\Presentation\\\\Filament/',
                'App\Filament',
                $content
            );

            if ($content !== $original) {
                file_put_contents($item->getPathname(), $content);
                echo "Updated Test: " . $item->getPathname() . "\n";
            }
        }
    }
}

recursivelyUpdateNamespaces($appDir);
recursivelyUpdateNamespaces($sourceDir);
fixTestNamespacesAndUses($basePath . '/tests');
fixTestNamespacesAndUses($basePath . '/routes');
fixTestNamespacesAndUses($basePath . '/bootstrap');

echo "Refactor script finished.\n";
