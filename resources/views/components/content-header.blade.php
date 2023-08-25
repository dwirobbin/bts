@props(['title'])

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-sm-6">
                <h1>{{ $title }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class='breadcrumb-item'>
                        <a href="{{ url('dashboard/index') }}">Dashboard</a>
                    </li>
                    @if (!request()->is('dashboard/index'))
                        <li class="breadcrumb-item active">
                            {{ $title }}
                        </li>
                    @endif
                </ol>
            </div>
        </div>
    </div>
</section>
