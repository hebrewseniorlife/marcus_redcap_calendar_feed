<?php

use Twig\Environment as Environment;
use Twig\Loader\FilesystemLoader as FilesystemLoader;
use Twig\Extra\String\StringExtension as StringExtension;
use Twig\Extension\DebugExtension as DebugExtension;
use Twig\Loader\ArrayLoader as ArrayLoader;

/**
 * TemplateEngine - Wrapper for interfacing with Twig template engine 
 */
class TemplateEngine{
    protected $environment;
    
    /**
     * __construct
     *
     * @param  mixed $templateFolder
     * @param  mixed $debug
     * @return void
     */
    function __construct(string $templateFolder = "", bool $debug = false) {
        $this->environment = new Environment(new FilesystemLoader($templateFolder),['debug' => $debug]);
        $this->environment->addExtension(new StringExtension());

        if ($debug){
            $this->environment->addExtension(new DebugExtension());
        }
    }
    
    /**
     * render - Creates a formatted string based on the named template and context object provided.
     *
     * @param  mixed $template
     * @param  mixed $context
     * @return string
     */
    public function render(string $template, array $context) : string {
        return $this->environment->render($template, $context);
    } 
    
    /**
     * renderTemplate - Creates a formatted string based on the template (string) and context provided
     *
     * @param  mixed $stringTemplate
     * @param  mixed $context
     * @return string
     */
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