<?php

namespace Scripts;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class ComposerScripts
{
    /**
     * @param \Composer\Script\Event $event
     */
    public static function postAutoloadDump($event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        require_once $vendorDir.'/autoload.php';
        self::replaceCleanBindingsBody($vendorDir.'/illuminate/database/Query/Builder.php');
    }

    private static function replaceCleanBindingsBody(string $srcPath)
    {
        $code = file_get_contents($srcPath);

        // Parse the source code.
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse($code);
        
        // Find the node of the cleanBindings function in the AST.
        $nodeFinder = new NodeFinder();
        $builderClass = $nodeFinder->findFirstInstanceOf($stmts, Node\Stmt\Class_::class);
        $cleanBindingsFunction = $nodeFinder->findFirst($builderClass->stmts, function(Node $node) {
            return $node instanceof Node\Stmt\ClassMethod
                && $node->name->toString() === 'cleanBindings';
        });

        $newCleanBindingsCode = <<<'PHP'
    <?php
    function donor() {
        // Up to you
        return ['25 or 6 to 4'];
    }
    PHP
        ;

        // Transplant the body of the donor to our patient.
        $donorFunction = $parser->parse($newCleanBindingsCode)[0];
        $cleanBindingsFunction->stmts = $donorFunction->stmts;

        // Dump PHP code.
        $prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
        $newCode = $prettyPrinter->prettyPrintFile($stmts);
        
        file_put_contents($srcPath, $newCode);
    }
}