@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop


@section('content')
    <div class="container">


        <div class="starter-template">
            <h1>@lang('file-transfer::front_lang.bundle-preview-title')</h1>
        </div>
        @if (!empty($metadata['files']) && count($metadata['files']) > 0)

            @lang('file-transfer::front_lang.bundle-preview-intro')<br />
            @lang('file-transfer::front_lang.download-all-or-one')<br /><br />

            <ul id="files-list">
                @foreach ($metadata['files'] as $f)
                    <li>
                        <a href="{{ route('file-transfer.file.download', ['bundle' => $bundle_id, 'file' => $f['filename'], 'auth' => $metadata['view-auth'] ]) }}">
                            {{ $f['original'] }}
                        </a>
                        <span class="filesize">({{ Clavel\FileTransfer\Services\Upload::humanFilesize($f['filesize']) }})</span>
                    </li>
                @endforeach
            </ul>

            @if (count($metadata['files']) > 1)
                <p class="download-all-btn">
                    <a href="{{ route('file-transfer.bundle.download', ['bundle' => $bundle_id, 'auth' => $metadata['view-auth'] ])}}">
                        @lang('file-transfer::front_lang.download-all')
                    </a>
                    <br />
                    <span class="bundle-info">
					({{ Clavel\FileTransfer\Services\Upload::humanFilesize($metadata['fullsize']) }} @lang('file-transfer::front_lang.for') {{ count($metadata['files']) }} {{ trans_choice('app.files', count($metadata['files'])) }})
				</span>
                </p>
            @else
                <p class="download-all-btn">
                    <a href="{{ route('file-transfer.file.download', ['bundle' => $bundle_id, 'file' => $f['filename'], 'auth' => $metadata['view-auth'] ])}}">
                        @lang('file-transfer::front_lang.download-all')
                    </a>
                    <br />
                    <span class="bundle-info">
					({{ Clavel\FileTransfer\Services\Upload::humanFilesize($metadata['fullsize']) }} @lang('file-transfer::front_lang.for') {{ count($metadata['files']) }} {{ trans_choice('app.files', count($metadata['files'])) }})
				</span>
                </p>
            @endif

            @if (!empty($metadata['expires_at_carbon']))
                <p class="expiry-warning">
                    @lang('file-transfer::front_lang.warning-bundle-expiration', ['date' => $metadata['expires_at_carbon']->diffForHumans()])
                </p>
            @endif
        @else
            <p class="error">@lang('file-transfer::front_lang.no-file-in-this-bundle')</p>
        @endif

    </div><!-- /.container -->



@endsection

@section("foot_page")


@stop


