<?php


namespace App\Http\Middleware;

use Closure;

class ContentNegotiatorMiddleware
{
    public $defaultType = 'text/html';

    public $acceptableTypes = [
        'text/html' => 'html',
        'application/xhtml+xml' => 'html',
        'text/n3' => 'data',
        'text/turtle' => 'data',
        'application/n-triples' => 'data',
        'application/rdf+xml' => 'data',
    ];

    public $fileExtensions = [
        'text/html' => '.html',
        'application/xhtml+xml' => '.html',
        'text/turtle' => '.ttl',
        'application/n-triples' => '.nt',
        'application/rdf+xml' => '.rdf',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $datasetName = explode('.', $request->route()[1]['as'])[1];

        // parse HTTP "Accept" header
        $accepts = [];
        foreach (explode(',', $request->header('accept')) as $accept) {
            $accept = explode(';', $accept);
            if (count($accept) === 2 && preg_match('/q=([\d\.]+)/', $accept[1], $m)) {
                $accepts[trim($accept[0])] = (float)$m[1];
            } elseif (count($accept) !== 2) {
                $accepts[trim($accept[0])] = 1.0;
            }
        }
        arsort($accepts);

        // 303 redirect
        $id = $request->route('id');
        $negotiatedType = $this->defaultType;
        foreach ($accepts as $mime => $q) {
            if (array_key_exists($mime, $this->acceptableTypes)) {
                $negotiatedType = $mime;
            }
        }

        $redirectTo = $this->acceptableTypes[$negotiatedType] . '.' . $datasetName;
        $params = ['id' => $id];
        if (substr($redirectTo, 0, 4) === 'data') {
            $params['ext'] = $this->fileExtensions[$negotiatedType];
        }
        return redirect()->route($redirectTo, $params, 303);
    }
}
