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
                </li>
            </div>
        </div>

        <div class="float-right">
            <div class="dropdown">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="{{ $dataUri }}.nt">N-Triples</a>
                    <a class="dropdown-item" href="{{ $dataUri }}.ttl">Turtle</a>
                    <a class="dropdown-item" href="{{ $dataUri }}.json">JSON</a>
                    <a class="dropdown-item" href="{{ $dataUri }}.jsonld">JSON-LD</a>
                    <a class="dropdown-item" href="{{ $dataUri }}.rdf">RDF/XML</a>
                </div>
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
        @foreach($graph->toRdfPhp() as $subject => $predicateObjects)
            @if($subject === $primaryTopic)
                @foreach($predicateObjects as $predicate => $objects)
                    @include('parts.row')
                @endforeach
                @break
            @endif
        @endforeach
        @foreach($graph->toRdfPhp() as $subject => $predicateObjects)
            @if($subject !== $primaryTopic)
                @foreach($predicateObjects as $predicate => $objects)
                    @include('parts.row')
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>

    <script type="application/ld+json">
        {!! $graph->serialise('jsonld') !!}
    </script>
@endsection
