<?php

namespace App\Http\Middleware;

use Closure;

class DatasetMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->route()) {
            return $next($request);
        }

        $datasetName = explode('.', $request->route()[1]['as'])[1];
        $currentDatasetConfig = config('datasets.' . $datasetName);

        $id = $request->route('id');
        $resourceUri = str_replace('{id}', $id, $currentDatasetConfig['resource_uri']);
        $dataUri = str_replace('{id}', $id, $currentDatasetConfig['data_uri']);

        $request->merge([
            'datasetConfig' => $currentDatasetConfig,
            'resourceUri' => $resourceUri,
            'dataUri' => $dataUri,
        ]);

        return $next($request);
    }
}
