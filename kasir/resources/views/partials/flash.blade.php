@if (session('ok'))<div class="alert ok" role="status">{{ session('ok') }}</div>@endif
@if ($errors->has('void') || $errors->has('items'))
    <div class="alert bad" role="alert">{{ $errors->first('void') ?: $errors->first('items') }}</div>
@endif
