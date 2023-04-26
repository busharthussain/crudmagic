<thead>
<tr>
    @foreach($headers as $key => $header)
        <th>
            {{ strtoupper($header['name']) }}
            @if(!empty($header['isSorter']))
                <i class="sorting fa fa-fw fa-sort" id="{{$header['key']}}"></i>
            @endif
        </th>
    @endforeach
</tr>
</thead>
