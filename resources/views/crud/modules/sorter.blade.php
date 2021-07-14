@php
$tableName = (new $modelClass())->getTable();
$sortParams = ['sort_col' => $colname];
$sortParams = request()->search ?
    array_merge($sortParams, ['search' => request()->search]) : $sortParams;
@endphp
@if(!session("crud.$tableName.sort_way") or session("crud.$tableName.sort_col") !== $colname or (session("crud.$tableName.sort_way") === 'desc' and session("crud.$tableName.sort_col") === $colname))
<a class="col_sort text-secondary lead asc" href="{{ route("bo.$tableName.index", array_merge($sortParams, ['sort_way' => 'asc'])) }}">&nbsp;&darr;&nbsp;</a>
@endif
@if(!session("crud.$tableName.sort_way") or session("crud.$tableName.sort_col") !== $colname or (session("crud.$tableName.sort_way") === 'asc' and session("crud.$tableName.sort_col") === $colname))
<a class="col_sort text-secondary lead desc" href="{{ route("bo.$tableName.index", array_merge($sortParams, ['sort_way' => 'desc'])) }}">&nbsp;&uarr;&nbsp;</a>
@endif
