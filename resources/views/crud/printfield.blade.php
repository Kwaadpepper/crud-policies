@switch($action)
    @case(CrudAction::viewAny())
        @switch($props['type'])
            @case(CrudType::email())
                <a href="mailto:$field">{{ $field }}</a>
                @break
            @case(CrudType::enum())
                {{ $model->{$fieldName}->label }}
                @break
            @case(CrudType::belongsTo())
                {{ $prop['belongsTo']::find($model->{$fieldName})->{$model->crudLabelColumn} }}
                @break
            @case(CrudType::belongsToMany())
                {{ $model->{$fieldName}->implode($prop['belongsToManyLabel'], ',') }}
                @break
            @case(CrudType::boolean())
                @if($model->{$fieldName})OUI @else NON @endif
                @break
            @case(CrudType::image())
                <img class="img-responsive" src="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" alt="{{ sprintf('/storage/%s', $model->{$fieldName}) }}">
                @break
            @default
                {{ $field }}
        @endswitch
        @break

    @case(CrudAction::view())
        @switch($props['type'])
            @case(CrudType::email())
                <a href="mailto:$field">{{ $field }}</a>
                @break
            @case(CrudType::enum())
                {{ $model->{$fieldName}->label }}
                @break
            @case(CrudType::json())
                @json($field)
                @break
            @case(CrudType::boolean())
                @if($model->{$fieldName})OUI @else NON @endif
                @break
            @case(CrudType::image())
                <img class="img-responsive" src="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" alt="{{ sprintf('/storage/%s', $model->{$fieldName}) }}">
                @break
            @case(CrudType::belongsTo())
                {{ $prop['belongsTo']::find($model->{$fieldName})->{$model->crudLabelColumn} }}
                @break
            @case(CrudType::belongsToMany())
                {{ $model->{$fieldName}->implode($prop['belongsToManyLabel'], ',') }}
                @break
            @default
                {{ $field }}
        @endswitch
        @break

    @default
        @dd('unknown action')
@endswitch
