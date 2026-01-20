@php
    /** @var \EasyRdf\Graph $graph */
    /** @var string $primaryTopic */
    /** @var string $dataUri */
@endphp
@extends('base')
@section('title')
    About: {{ $graph->label($primaryTopic) ?? $graph->getLiteral($primaryTopic, 'schema:name') }}
@endsection
@section('content')
    <header>
        <h1>@yield('title')</h1>
        <div>
            <code class="h5">{{ $primaryTopic }}</code>
            <div>
                An Entity of Type:
                <ul class="list-inline" style="display: inline">
                    @foreach($graph->typesAsResources($primaryTopic) as $type)
                        <li class="list-inline-item"><a href="{{ $type->getUri() }}">
                                {{ $type->shorten() ?? $type->getUri() }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="float-end">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ $dataUri }}.nt">N-Triples</a></li>
                    <li><a class="dropdown-item" href="{{ $dataUri }}.ttl">Turtle</a></li>
                    <li><a class="dropdown-item" href="{{ $dataUri }}.json">JSON</a></li>
                    <li><a class="dropdown-item" href="{{ $dataUri }}.jsonld">JSON-LD</a></li>
                    <li><a class="dropdown-item" href="{{ $dataUri }}.rdf">RDF/XML</a></li>
                </ul>
            </div>
        </div>
    </header>

    <table class="table table-sm mt-3">
        <thead>
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        </thead>
        <tbody>
        @php
            $resource = $graph->resource($primaryTopic);
        @endphp
        {{-- メインのリソース ($primaryTopic) のプロパティを表示 --}}
        @foreach($resource->properties() as $predicate)
            @php
                $objects = $resource->all($predicate);
            @endphp
            @include('parts.row')
        @endforeach
        {{-- メインのリソース以外、かつブランクノードでないリソースを表示 --}}
        @foreach($graph->resources() as $uri => $otherResource)
            @if($uri !== $primaryTopic && !$otherResource->isBNode())
                @foreach($otherResource->properties() as $predicate)
                    @php
                        $objects = $otherResource->all($predicate);
                    @endphp
                    @include('parts.row', ['subject' => $uri])
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>

    <script type="application/ld+json">
        {!! $graph->serialise('jsonld') !!}
    </script>
@endsection
