<?php

namespace Chtombleson\SSHeadless\Controller;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injectable;
use Chtombleson\SSHeadless\Extension\StaticJson;

class APIController extends Controller
{
    private static $allowed_actions = [
        'index',
    ];

    public function index(HTTPRequest $request)
    {
        $guid = $request->param('ID');

        if (!$request->isGET()) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (empty($guid)) {
            return $this->json(['error' => 'Bad request'], 400);
        }

        $classes = [];

        foreach (ClassInfo::subclassesFor(DataObject::class) as $class) {
            if (Injectable::singleton($class)->hasExtension(StaticJson::class)) {
                $item = $class::get()->filter('GUID', $guid)->first();

                if ($item) {
                    break;
                }
            }
        }

        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        return $this->json($item->JSON);
    }


    protected function json($body, $code = 200, $expiry = 0)
    {
        $body = json_encode($body);

        $response = (new HTTPResponse())
            ->addHeader('Content-Type', 'application/json')
            ->setStatusCode($code)
            ->setBody($body);

        return $response;
    }
}
