<?php

namespace Bolt\Extension\Timonline\Contentlength;

use Bolt\Application;
use Bolt\BaseExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Extension extends \Bolt\BaseExtension
{

    public function initialize()
    {
        // Automatically set Content-Length header
        $this->app->after(function (Request $request, Response $response) {
            // If it's a HEAD request: don't set content-length
            if ($request->getMethod() == 'HEAD') {
                return $response;
            }

            // If the response already has a Content-Length header: skip it
            if ($response->headers->has('Content-Length')) {
                return $response;
            }

            // Messages MUST NOT include both a Content-Length header field and
            // a non-identity transfer-coding. If the message does include a
            // non-identity transfer-coding, the Content-Length MUST be ignored.
            // (RFC 2616, Section 4.4)
            if ($response->headers->has('Transfer-Encoding')) {
                return $response;
            }

            $length = strlen($response->getContent());
            if ($length) {
                $response->headers->set('Content-Length', $length);
            }
            return $response;
        }, Application::LATE_EVENT);
    }
}
