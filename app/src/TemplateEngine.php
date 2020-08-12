<?php

use Twig\Environment as Environment;
use Twig\Loader\FilesystemLoader as FilesystemLoader;
use Twig\Extra\String\StringExtension as StringExtension;
use Twig\Extension\DebugExtension as DebugExtension;
use Twig\Loader\ArrayLoader as ArrayLoader;

class TemplateEngine{
    protected $environment;

    function __construct(string $templateFolder = "", bool $debug = true){
        $this->environment = new Environment(new FilesystemLoader($templateFolder),['debug' => $debug]);
        $this->environment->addExtension(new StringExtension());

        if ($debug){
            $this->environment->addExtension(new DebugExtension());
        }
    }

    public function render(string $template, array $context) : string {
        return $this->environment->render($template, $context);
    } 

    public static function renderTemplate(? string $stringTemplate = "", array $context) : string {
        if (empty($stringTemplate)){
            return "";
        }

        $environment = new Environment(new ArrayLoader([]));
        $environment->addExtension(new StringExtension());
        
        $template = $environment->createTemplate($stringTemplate);
        return $template->render($context); 
    }
}