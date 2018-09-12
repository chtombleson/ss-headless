<?php

namespace Chtombleson\SSHeadless\Output;

interface WriterInterface
{
    public function write(string $guid, array $json);

    public function remove(string $guid);
}
