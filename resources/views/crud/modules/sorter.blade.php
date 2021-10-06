<tr>
    @foreach ($modelClass::getEditableProperties() as $colname => $prop)
    @if(in_array(CrudAction::viewAny(), $prop['actions']))
    @switch($prop['type'])
        @case(CrudType::password())
            {{-- Ignore --}}
            @break
        @default
            <th scope="col">
                {{ $prop['label'] }}
                @if(!session("crud.$modelTable.sort_way") or session("crud.$modelTable.sort_col") !== $colname or (session("crud.$modelTable.sort_way") === 'desc' and session("crud.$modelTable.sort_col") === $colname))
                <a class="col_sort text-secondary lead asc" href="{{ request()->fullUrlWithQuery(['sort_col' => $colname, 'sort_way' => 'asc', 'rst' => null]) }}">&nbsp;&darr;&nbsp;</a>
                @endif
                @if(!session("crud.$modelTable.sort_way") or session("crud.$modelTable.sort_col") !== $colname or (session("crud.$modelTable.sort_way") === 'asc' and session("crud.$modelTable.sort_col") === $colname))
                <a class="col_sort text-secondary lead desc" href="{{ request()->fullUrlWithQuery(['sort_col' => $colname, 'sort_way' => 'desc', 'rst' => null]) }}">&nbsp;&uarr;&nbsp;</a>
                @endif
            </th>
    @endswitch
    @endif
    @endforeach
    <th scope="col">
        @if(session("crud.$modelTable.sort_way") and session("crud.$modelTable.sort_col"))
        <a href="{{ request()->fullUrlWithQuery(['rst' => 'true','sort_col' => null, 'sort_way' => null]) }}" class="btn btn-small btn-danger">&#9003;</a>
        @endif
    </th>
</tr>
