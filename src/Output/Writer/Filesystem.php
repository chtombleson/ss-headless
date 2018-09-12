<?php

namespace Chtombleson\SSHeadless\Output\Writer;

use Chtombleson\SSHeadless\Exception;
use Chtombleson\SSHeadless\Output\WriterInterface;
use SilverStripe\Core\Config\Configurable;

class Filesystem implements WriterInterface
{
    use Configurable;

    public function write(string $guid, array $json)
    {
        $outputDir = $this->config()->get('output_dir');

        if (empty($outputDir)) {
            throw new Exception('Filesystem: no output_dir is set.');
        }

        $filePath = BASE_PATH . DIRECTORY_SEPARATOR . $outputDir;

        if (!file_exists($filePath)) {
            mkdir($filePath, 0755);
        }

        return file_put_contents($filePath . DIRECTORY_SEPARATOR . $guid . '.json', json_encode($json));
    }

    public function remove(string $guid)
    {
        $outputDir = $this->config()->get('output_dir');

        if (empty($outputDir)) {
            throw new Exception('Filesystem: no output_dir is set.');
        }

        $filePath = BASE_PATH . DIRECTORY_SEPARATOR . $outputDir . DIRECTORY_SEPARATOR . $guid . '.json';

        if (!file_exists($filePath)) {
            return false;
        }

        return unlink($filePath);
    }
}
