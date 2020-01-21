<tr>
    <td>
        @if($subject !== $primaryTopic)
            is
        @endif
        <a href="{{ $predicate }}">{{ \EasyRdf_Namespace::shorten($predicate) ?? $predicate }}</a>
        @if($subject !== $primaryTopic)
            of
        @endif
    </td>
    <td>
        <ul>
            @if($subject === $primaryTopic)
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
            @else
                <a href="{{ $subject }}">{{ $subject }}</a>
            @endif
        </ul>
    </td>
</tr>
