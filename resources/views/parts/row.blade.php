<tr>
    <td>
        @if(isset($subject) && $subject !== $primaryTopic)
            is
        @endif
        <a href="{{ $predicate }}">{{ \EasyRdf\RdfNamespace::shorten($predicate) ?? $predicate }}</a>
        @if(isset($subject) && $subject !== $primaryTopic)
            of
        @endif
    </td>
    <td>
        <ul>
            @if(!isset($subject) || $subject === $primaryTopic)
                @foreach($objects as $object)
                    @if($object instanceof \EasyRdf\Resource)
                        <li style="list-style: none;">
                            @if($object->isBNode())
                                <span class="bnode">
                                    {{-- Blank Node の詳細を表示 --}}
                                    <table class="table table-sm table-bordered">
                                        @foreach($object->properties() as $bnodePredicate)
                                            @include('parts.row', [
                                                'predicate' => $bnodePredicate,
                                                'objects' => $object->all($bnodePredicate),
                                                'primaryTopic' => $object->getUri(),
                                                // 再帰であることを示すために、または無限ループ防止のために必要に応じて変数を渡す
                                                'subject' => $object->getUri()
                                            ])
                                        @endforeach
                                    </table>
                                </span>
                            @else
                                <a href="{{ $object->getUri() }}">{{ $object->shorten() ?? $object->getUri() }}</a>
                            @endif
                        </li>
                    @else
                        <li>
                            {{-- Literal --}}
                            @php
                                $value = $object->getValue();
                            @endphp
                            @if($object->getDatatype() === 'xsd:date')
                                {{ $value->format('Y-m-d') }}
                            @elseif($value instanceof \DateTime)
                                {{ $value->format('Y-m-d h:i:s') }}
                            @else
                                {{ $value }}
                            @endif
                            {{-- language tag --}}
                            @if($object->getLang())
                                <small class="langtag">{{ '@'.$object->getLang() }}</small>
                            @endif
                            {{-- data type --}}
                            @if($object->getDatatype())
                                <small
                                    class="datatype">^^{{ \EasyRdf\RdfNamespace::shorten($object->getDatatype()) ?? $object->getDatatype() }}</small>
                            @endif
                        </li>
                    @endif
                @endforeach
            @else
                <li>
                    <a href="{{ $subject }}">{{ \EasyRdf\RdfNamespace::shorten($subject) ?? $subject }}</a>
                </li>
            @endif
        </ul>
    </td>
</tr>
