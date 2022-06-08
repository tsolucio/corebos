<?php

namespace Vaites\ApacheTika\Metadata;

/**
 * Metadata class for documents
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class DocumentMetadata extends Metadata
{
    /**
     * Title (if not detected by Apache Tika, name without extension is used)
     *
     * @var string
     */
    public $title = null;

    /**
     * Description.
     *
     * @var string
     */
    public $description = null;

    /**
     * Keywords
     *
     * @var array
     */
    public $keywords = [];

    /**
     * Two-letter language code (ISO-639-1)
     *
     * @link https://en.wikipedia.org/wiki/ISO_639-1
     *
     * @var string
     */
    public $language = null;

    /**
     * Content encoding
     *
     * @var string
     */
    public $encoding = null;

    /**
     * Author
     *
     * @var string
     */
    public $author = null;

    /**
     * Software used to generate document
     *
     * @var string
     */
    public $generator = null;

    /**
     * Number of pages
     *
     * @var int
     */
    public $pages = 0;

    /**
     * Number of words.
     *
     * @var int
     */
    public $words = 0;

    /**
     * Sets an attribute
     *
     * @param mixed $value
     * @throws  \Exception
     */
    protected function setSpecificAttribute(string $key, $value): MetadataInterface
    {
        if(is_array($value))
        {
            $value = array_shift($value);
        }

        switch(mb_strtolower($key))
        {
            case 'dc:title':
            case 'title':
                $this->title = $value;
                break;

            case 'comments':
            case 'w:Comments':
                $this->description = $value;
                break;

            case 'keyword':
            case 'keywords':
            case 'meta:keyword':
                $keywords = preg_split(preg_match('/,/', $value) ? '/\s*,\s*/' : '/\s+/', $value);
                $this->keywords = array_unique($keywords ?: []);
                break;

            case 'language':
                $this->language = mb_substr($value, 0, 2);
                break;

            case 'author':
            case 'dc:creator':
            case 'initial-creator':
                $this->author = $value;
                break;

            case 'application-name':
            case 'generator':
            case 'producer':
                $value = preg_replace('/\$.+/', '', $value);
                $this->generator = trim($value);
                break;

            case 'nbpage':
            case 'page-count':
            case 'xmptpg:npages':
                $this->pages = (int) $value;
                break;

            case 'nbword':
            case 'word-count':
                $this->words = (int) $value;
                break;

            case 'content-encoding':
                $this->encoding = $value;
                break;

            case 'x-tika:content':
                $this->content = $value;
                break;
        }

        return $this;
    }
}
