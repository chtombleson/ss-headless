<?php

namespace Chtombleson\SSHeadless\Output;

use Chtombleson\SSHeadless\Exception;
use SilverStripe\Core\Config\Configurable;

class Factory
{
    use Configurable;

    private static $instance;

    protected $writer;

    public function __construct()
    {
        $writerClass = $this->config()->get('writer');

        if (!class_exists($writerClass)) {
            throw new Exception('Class: ' . $writerClass . ' does not exist.');
        }

        $writer = new $writerClass;

        if (!$writer instanceOf WriterInterface) {
            throw new Exception('Writer does not implement WriterInterface.');
        }

        $this->writer = $writer;
    }

    public function write(string $guid, array $json)
    {
        return $this->writer->write($guid, $json);
    }

    public function remove(string $guid)
    {
        return $this->writer->remove($guid);
    }

    public function getAbsoluteUrl(string $guid)
    {
        return $this->writer->getAbsoluteUrl($guid);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
