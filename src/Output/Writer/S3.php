<?php

namespace Chtombleson\SSHeadless\Output\Writer;

use Chtombleson\SSHeadless\Exception;
use Chtombleson\SSHeadless\Output\WriterInterface;
use SilverStripe\Core\Config\Configurable;
use Aws\S3\S3Client;

class S3 implements WriterInterface
{
    use Configurable;

    private $client;

    public function __construct()
    {
        if (!$this->config()->get('region') ||
            !$this->config()->get('key') ||
            !$this->config()->get('secret') ||
            !$this->config()->get('bucket')
        ) {
            throw new Exception('S3 requires the following settings: region, key, secret, bucket.');
        }

        $this->client = new S3Client([
            'region'  => '-- your region --',
            'version' => 'latest',
            'credentials' => [
                'key'    => "-- access key id --",
                'secret' => "-- secret access key --",
            ]
        ]);
    }

    public function write(string $guid, array $json)
    {
        try {
            $this->client->putObject([
                'Bucket' => $this->config()->get('bucket'),
                'Key' => $guid,
                'Body' => json_encode($json),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function remove(string $guid)
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->config()->get('bucket'),
                'Key' => $guid,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAbsoluteUrl(string $guid)
    {
        return null;
    }
}
