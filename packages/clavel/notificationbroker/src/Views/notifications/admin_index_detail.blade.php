

    @if(!empty($notification) && !empty($payload))
        <div class="row">
            <div class="col-md-12">
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.emisor') !!}:</strong>
                    {{$notification->user->userProfile->fullName}} ({{$notification->user->email}})
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.sent_at') !!}:</strong> {{$notification->sentAtFormatted}}
                </div>
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.tipo') !!}:</strong> {{explode("/", $notification->slug_type)[0]}}
                </div>
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.guid') !!}:</strong> {{$notification->guid}}
                </div>
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.status') !!}:</strong> {{$notification->status->name}}
                </div>
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.retries') !!}:</strong> {{$notification->retriesFormatted}}
                </div>
            </div>

            <div class="col-md-4">
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.to') !!}:</strong> {{$notification->receiver}}
                </div>
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.response_info') !!}:</strong> {{!empty($notification->response_info)?$notification->response_info:''}}
                </div>
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.credits') !!}:</strong> {{ number_format($notification->credits,2,",",".") }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div>
                    <strong>{!! trans('notificationbroker::notifications/admin_lang.payload') !!}:</strong> {{$notification->payload}}
                </div>
            </div>
        </div>

    @endif



