<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ContentNegotiatorMiddleware;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    protected $acceptableFileExtensions = [
        '.json',
        '.jsonld',
        '.nt',
        '.rdf',
        '.ttl',
    ];

    /**
     * ResourceController constructor.
     */
    public function __construct()
    {
        $this->middleware('dataset');
    }

    /**
     * @param $request
     * @return \EasyRdf\Graph
     */
    protected function querySparql($request)
    {
        if (!empty($request->datasetConfig['http'])) {
            $httpClient = new \EasyRdf\Http\Client(null, $request->datasetConfig['http']);
            \EasyRdf\Http::setDefaultHttpClient($httpClient);
        }
        $client = new \EasyRdf\Sparql\Client($request->datasetConfig['endpoint']);
        $query = <<<EOT
    CONSTRUCT {
        <{$request->resourceUri}> ?p ?o .
        ?s ?ip <{$request->resourceUri}> .
    }
    WHERE {
        <{$request->resourceUri}> ?p ?o .
        OPTIONAL { ?s ?ip <{$request->resourceUri}> . }
    }
EOT;
        return $client->query($query);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\View\View
     */
    public function html(Request $request, $id)
    {
        $graph = $this->querySparql($request);
        if ($graph->isEmpty()) {
            abort(404);
        }

        return view('resource')->with([
            'graph' => $graph,
            'primaryTopic' => $request->resourceUri,
            'dataUri' => $request->dataUri,
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $ext
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function data(Request $request, $id, $ext)
    {
        if (!in_array($ext, $this->acceptableFileExtensions)) {
            abort(400);
        }

        $graph = $this->querySparql($request);
        try {
            $data = $graph->serialise(substr($ext, 1));
        } catch (\EasyRdf\Exception $e) {
            abort(400);
        }
        $type = ContentNegotiatorMiddleware::mimetypeFromExtension($ext);
        return response($data)
            ->header('Content-Type', $type);
    }
}
