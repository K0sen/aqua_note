<?php

namespace AppBundle\Twig;


use AppBundle\Services\MarkdownTransformer;

class MarkdownExtension extends \Twig_Extension
{
    private $markdownTransformer;

    public function __construct(MarkdownTransformer $markdownTransformer)
    {
        $this->markdownTransformer = $markdownTransformer;
    }
    public function getName()
    {
        return 'app_markdown';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdownify', array($this, 'parseMarkdown'))
        ];
    }

    public function parseMarkdown($str)
    {
        return $this->markdownTransformer->parse($str);
    }

}