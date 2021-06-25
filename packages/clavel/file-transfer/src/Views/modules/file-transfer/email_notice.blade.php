@extends('front.email.default')

@section('content')
    <p>
        <span style='font-size:16px'> Se han enviado los siguientes ficheros por parte de {{ $email }}</span><br><br>
        {{ $bundle->files->count() }} elementos, {{ Clavel\FileTransfer\Services\Upload::humanFilesize($bundle->fullsize) }} en total ・
        Se eliminará el {{ $bundle->expiresAtFormattedHumans }}<br><br>

        <strong>Destinatarios:</strong><br>
        {{ $email_destino }}<br><br>

        <strong>Mensaje:</strong><br>
        {{ $mensaje }}<br><br>

        <strong>Enlace de descarga:</strong><br>
        <a href="{{ route('file-transfer.bundle.download', [
                    'bundle' 		=> $bundle->bundle_key,
                    'auth'			=> $bundle->view_auth
                ]) }}">
            {{ route('file-transfer.bundle.download', [
                        'bundle' 		=> $bundle->bundle_key,
                        'auth'			=> $bundle->view_auth
                    ]) }}<br><br>
        </a><br><br>

        <strong>Enlace de visualización:</strong><br>
        <a href="{{ route('file-transfer.bundle.preview', [
                    'bundle' 		=> $bundle->bundle_key,
                    'auth'			=> $bundle->view_auth
                ]) }}">
            {{ route('file-transfer.bundle.preview', [
                        'bundle' 		=> $bundle->bundle_key,
                        'auth'			=> $bundle->view_auth
                    ]) }}<br><br>
        </a><br><br>


        <strong>Enlace de borrado:</strong><br>
        <a href="{{ route('file-transfer.bundle.delete', [
                    'bundle' 		=> $bundle->bundle_key,
                    'auth'			=> $bundle->delete_auth
                ]) }}">
            {{ route('file-transfer.bundle.delete', [
                        'bundle' 		=> $bundle->bundle_key,
                        'auth'			=> $bundle->delete_auth
                    ]) }}<br><br>
        </a><br><br>

        Saludos cordiales<br>
    </p>
@endsection