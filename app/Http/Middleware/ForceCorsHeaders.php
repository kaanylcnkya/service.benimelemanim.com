<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCorsHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'https://benimelemanim.com',
            'https://www.benimelemanim.com',
            'https://service.benimelemanim.com',
        ];

        $origin = $request->headers->get('Origin');

        $headers = [];

        if ($origin && in_array($origin, $allowedOrigins, true)) {
            $headers['Access-Control-Allow-Origin'] = $origin;
            $headers['Vary'] = 'Origin';
        }

        $headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
        $headers['Access-Control-Allow-Headers'] = 'Content-Type, Accept, Authorization, X-Requested-With';
        $headers['Access-Control-Max-Age'] = '86400';

        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)->withHeaders($headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}