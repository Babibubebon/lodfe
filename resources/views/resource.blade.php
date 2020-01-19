@extends('base')
@section('title')
    About: {{ $graph->label($subject) ?? $graph->getLiteral($subject, 'schema:name') }}
@endsection

@section('content')
    <section>
        <h1>@yield('title')</h1>
        <code class="h5">{{ $subject }}</code>

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
    </section>

    <table class="table table-sm mt-3">
        <thead>
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        </thead>
        @foreach($graph->toRdfPhp() as $subject => $predicateObjects)
            @foreach($predicateObjects as $predicate => $objects)
                <tbody>
                <tr>
                    <td>
                        <a href="{{ $predicate }}">{{ \EasyRdf_Namespace::shorten($predicate) ?? $predicate }}</a>
                    </td>
                    <td>
                        <ul>
                            @foreach($objects as $object)
                                <li>
                                    @if($object['type'] === 'uri')
                                        <a href="{{ $object['value'] }}">{{ $object['value'] }}</a>
                                    @elseif($object['type'] === 'literal')
                                        {{ $object['value'] }}
                                        @if(isset($object['lang']))
                                            <small>{{ '@'.$object['lang'] }}</small>
                                        @endif
                                        @if(isset($object['datatype']))
                                            <small>^^{{ \EasyRdf_Namespace::shorten($object['datatype']) ?? $object['datatype'] }}</small>
                                        @endif
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                </tbody>

            @endforeach
        @endforeach
    </table>

    <script type="application/ld+json">
    {!! $graph->serialise('jsonld') !!}
    </script>
@endsection
