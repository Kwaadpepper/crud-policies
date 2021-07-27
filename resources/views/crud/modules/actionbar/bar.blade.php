<div class="btn-group float-end" role="group" aria-label="{{ Str::plural(__('crud-policies::crud.action')) }}">
    @switch($action)
        @case(CrudAction::viewAny())
            @include('crud-policies::crud.modules.actionbar.create')
            @break
        @case(CrudAction::view())
            @include('crud-policies::crud.modules.actionbar.update')
            @include('crud-policies::crud.modules.actionbar.delete')
            @break
        @case(CrudAction::create())
            @break
        @case(CrudAction::update())
            @include('crud-policies::crud.modules.actionbar.view')
            @include('crud-policies::crud.modules.actionbar.delete')
            @break
    @endswitch
</div>
