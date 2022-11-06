<tr>
    <td>
        @if($subject !== $primaryTopic)
            is
        @endif
        <a href="{{ $predicate }}">{{ \EasyRdf\RdfNamespace::shorten($predicate) ?? $predicate }}</a>
        @if($subject !== $primaryTopic)
            of
        @endif
    </td>
    <td>
        <ul>
            @if($subject === $primaryTopic)
                @foreach(collect($objects)->sortBy('value') as $object)
                    <li>
                        @if($object['type'] === 'uri')
                            <a href="{{ $object['value'] }}">{{ \EasyRdf\RdfNamespace::shorten($object['value']) ?? $object['value'] }}</a>
                        @elseif($object['type'] === 'literal')
                            {{ $object['value'] }}
                            @if(isset($object['lang']))
                                <small class="langtag">{{ '@'.$object['lang'] }}</small>
                            @endif
                            @if(isset($object['datatype']))
                                <small class="datatype">^^{{ \EasyRdf\RdfNamespace::shorten($object['datatype']) ?? $object['datatype'] }}</small>
                            @endif
                        @endif
                    </li>
                @endforeach
            @else
                <a href="{{ $subject }}">{{ $subject }}</a>
            @endif
        </ul>
    </td>
</tr>
