<?php

namespace App\Http\Controllers;

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
     * @param Request $request
     * @return mixed
     */
    protected function getCurrentDatasetConfig($request) {
        $datasetName = explode('.', $request->route()[1]['as'])[1];
        return config('datasets.' . $datasetName);
    }

    /**
     * @param $request
     * @param $id
     * @return \EasyRdf_Graph
     */
    protected function querySparql($request, $id)
    {
        $datasetConfig = $this->getCurrentDatasetConfig($request);
        $client = new \EasyRdf_Sparql_Client($datasetConfig['endpoint']);

        $resourceUri = str_replace('{id}', $id, $datasetConfig['resource_uri']);
        $query = 'DESCRIBE <' . $resourceUri . '>';
        return $client->query($query);
    }

    public function html(Request $request, $id)
    {
        $graph = $this->querySparql($request, $id);
        $subject = key($graph->toRdfPhp());
        $datasetConfig = $this->getCurrentDatasetConfig($request);
        $dataUri = str_replace('{id}', $id, $datasetConfig['data_uri']);

        return view('resource')->with([
            'graph' => $graph,
            'subject' => $subject,
            'dataUri' => $dataUri,
        ]);
    }

    public function data(Request $request, $id, $ext)
    {
        if (!in_array($ext, $this->acceptableFileExtensions)) {
            abort(400);
        }

        $graph = $this->querySparql($request, $id);
        try {
            $data = $graph->serialise(substr($ext, 1));
        } catch (\EasyRdf_Exception $e) {
            abort(400);
        }
        return $data;
    }
}
