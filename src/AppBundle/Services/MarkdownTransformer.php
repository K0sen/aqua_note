<?php

namespace AppBundle\Services;

use Doctrine\Common\Cache\Cache;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{
    private $markdownParser;
    private $cache;

    public function __construct(MarkdownParserInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
//        $this->cache = $cache;
    }

    public function parse($str)
    {
//        $cache = $this->cache;
//        $key = md5($str);
//        if ($cache->contains($key)) {
//            return $cache->fetch($key);
//        }

        $str = $this->markdownParser
                ->transformMarkdown('asd'.$str);
//        $cache->save($key, $str);

        return $str;
    }
}