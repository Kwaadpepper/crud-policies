@switch($action)
    @case(CrudAction::viewAny())
        @switch($props['type'])
            @case(CrudType::text())
                <div class="ck-content">{!! clean($field) !!}</div>
                @break
            @case(CrudType::color())
                <span class="colorize badge">{{ $field }}</span>
                @include('crud-policies::crud.modules.color', ['crudColorShow' => true])
                @break
            @case(CrudType::email())
                <a href="mailto:$field">{{ $field }}</a>
                @break
            @case(CrudType::enum())
                {{ $model->{$fieldName}->label }}
                @break
            @case(CrudType::belongsTo())
                @php
                    $obj = null;
                    $obj = $model->{Str::singular((new $prop['belongsTo']())->getTable())} ??
                    $prop['belongsTo']::find($model->{$fieldName})->first();
                @endphp
                {{ $obj->{(new $prop['belongsTo']())->crudLabelColumn} }}
                @break
            @case(CrudType::belongsToMany())
                {{ $model->{$fieldName}->implode($prop['belongsToManyLabel'], ',') }}
                @break
            @case(CrudType::hasMany())
                {{ $model->{$fieldName}->implode($prop['hasManyLabel'], ',') }}
                @break
            @case(CrudType::boolean())
                @if($model->{$fieldName})OUI @else NON @endif
                @break
            @case(CrudType::image())
                <img class="img-fluid" src="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" alt="{{ sprintf('/storage/%s', $model->{$fieldName}) }}">
                @break
            @case(CrudType::file())
                <a href="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" target="_blank">{{ sprintf('/storage/%s', $model->{$fieldName}) }}</a>
                @break
            @case(CrudType::uri())
                <a href="{{ $model->{$fieldName} }}" target="_blank">{{ $model->{$fieldName} }}</a>
                @break
            @case(CrudType::order())
                @if(
                    Session::get("crud.$modelTable.sort_col") === $fieldName and
                    Session::get("crud.$modelTable.sort_way") === 'asc'
                )
                <span>
                    @if($model->{$fieldName} - 1 >= 0)
                    <form class="d-inline" action="{{ route('crud-policies.models.changeOrder', [
                        'modelTable' => $modelTable,
                        'modelId' => $model->id,
                        'colName' => $fieldName,
                        'newOrder' => $model->{$fieldName} - 1,
                        'oldUrl' => str_replace(array('+', '/'), array('-', '_'), base64_encode(request()->getRequestUri()))
                    ]) }}" method="POST">
                        @csrf
                        @method('put')
                        <button class="col_sort btn btn-xs btn-primary text-decoration-none fw-bold lead desc">↑</button>
                    </form>
                    &nbsp;
                    @else
                    <span class="ms-2 me-3">&nbsp;</span>
                    @endif
                    {{ $model->{$fieldName} }}
                    @if(${"$modelTable$fieldName"} !== $model->{$fieldName})
                    &nbsp;
                    <form class="d-inline" action="{{ route('crud-policies.models.changeOrder', [
                        'modelTable' => $modelTable,
                        'modelId' => $model->id,
                        'colName' => $fieldName,
                        'newOrder' => $model->{$fieldName} + 1,
                        'oldUrl' => str_replace(array('+', '/'), array('-', '_'), base64_encode(request()->getRequestUri()))
                    ]) }}" method="POST">
                        @csrf
                        @method('put')
                        <button class="col_sort btn btn-xs btn-primary text-decoration-none fw-bold lead asc">↓</button>
                    </form>
                    @else
                    <span class="ms-2 me-3">&nbsp;</span>
                    @endif
                </span>
                @else
                {{ $model->{$fieldName} }}
                @endif
                @break
            @default
                {{ $field }}
        @endswitch
        @break

    @case(CrudAction::view())
        @switch($props['type'])
            @case(CrudType::text())
                <div class="ck-content">{!! clean($field) !!}</div>
                @break
            @case(CrudType::color())
                <span class="colorize badge">{{ $field }}</span>
                @include('crud-policies::crud.modules.color', ['crudColorShow' => true])
                @break
            @case(CrudType::email())
                <a href="mailto:$field">{{ $field }}</a>
                @break
            @case(CrudType::enum())
                {{ $model->{$fieldName}->label }}
                @break
            @case(CrudType::date())
                {{ $model->{$fieldName}->format('d/m/Y') }}
                @break
            @case(CrudType::datetime())
                {{ $model->{$fieldName}->format('d/m/Y H:i') }}
                @break
            @case(CrudType::json())
                @json($field)
                @break
            @case(CrudType::boolean())
                @if($model->{$fieldName})OUI @else NON @endif
                @break
            @case(CrudType::image())
                <img class="img-fluid" src="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" alt="{{ sprintf('/storage/%s', $model->{$fieldName}) }}">
                @break
            @case(CrudType::file())
                <a href="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" target="_blank">{{ pathinfo($model->{$fieldName} ?? '', PATHINFO_BASENAME) }}</a>
                @break
            @case(CrudType::uri())
                <a href="{{ $model->{$fieldName} }}" target="_blank">{{ pathinfo($model->{$fieldName} ?? '', PATHINFO_BASENAME) }}</a>
                @break
            @case(CrudType::order())
                {{ $model->{$fieldName} }}
                @break
            @case(CrudType::belongsTo())
                {{ $prop['belongsTo']::find($model->{$fieldName})->{(new $prop['belongsTo']())->crudLabelColumn} }}
                @break
            @case(CrudType::belongsToMany())
                {{ $model->{$fieldName}->implode($prop['belongsToManyLabel'], ',') }}
                @break
            @case(CrudType::hasMany())
                {{ $model->{$fieldName}->implode($prop['hasManyLabel'], ',') }}
                @break
            @default
                {{ $field }}
        @endswitch
        @break

    @default
        @dd('unknown action')
@endswitch
