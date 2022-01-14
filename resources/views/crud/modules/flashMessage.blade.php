@if(config('crud-policies.displayAlerts', true))
@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('crud-policies::crud.close') }}"></button>
        <strong>{!! nl2br(e($message)) !!}</strong>
    </div>
@endif


@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('crud-policies::crud.close') }}"></button>
        <strong>{!! nl2br(e($message)) !!}</strong>
    </div>
@endif


@if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('crud-policies::crud.close') }}"></button>
        <strong>{!! nl2br(e($message)) !!}</strong>
    </div>
@endif


@if ($message = Session::get('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('crud-policies::crud.close') }}"></button>
        <strong>{!! nl2br(e($message)) !!}</strong>
    </div>
@endif


@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('crud-policies::crud.close') }}"></button>
        @if ($errors->any())
        <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif
    </div>
@endif
@endif
