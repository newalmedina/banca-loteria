<div class="box-header with-border">
    <h3 class="box-title">{{ trans("elearning::asignaturas/admin_lang.profesores") }}</h3>

    <div class="box-tools pull-right">
        <span class="label label-primary">{{ $grupo->userPivot->count() }} {{ trans("elearning::asignaturas/admin_lang.usuarios") }}</span>
    </div>
</div>

<ul class="users-list clearfix">
    @foreach($grupo->userPivot as $user)
        <li>
            @if(!empty($user->userProfile->photo) && $user->userProfile->photo!='')
                <img alt="User Image" src="{{ url('profile/getphoto/'.$user->userProfile->photo) }}" style="width: 128px; height: 128px;">
            @else
                <img alt="User Image" src="{{ asset("/assets/img/default_user.png") }}" style="width: 128px; height: 128px;">
            @endif
            <a href="#" class="users-list-name">{{ $user->userProfile->full_name }}</a>
            <span class="users-list-date">{{ $user->creacion }}</span>
        </li>
    @endforeach
</ul>

<div class="box-header with-border">
    <h3 class="box-title">{{ trans("elearning::asignaturas/admin_lang.alumnos") }}</h3>

    <div class="box-tools pull-right">
        <span class="label label-primary">{{ $grupo->userPivot->count() }} {{ trans("elearning::asignaturas/admin_lang.usuarios") }}</span>
    </div>
</div>

<ul class="users-list clearfix">
    @foreach($grupo->userPivot as $user)
        <li>
            @if(!empty($user->userProfile->photo) && $user->userProfile->photo!='')
                <img alt="User Image" src="{{ url('profile/getphoto/'.$user->userProfile->photo) }}" style="width: 128px; height: 128px;">
            @else
                <img alt="User Image" src="{{ asset("/assets/img/default_user.png") }}" style="width: 128px; height: 128px;">
            @endif
            <a href="#" class="users-list-name">{{ $user->userProfile->full_name }}</a>
            <span class="users-list-date">{{ $user->creacion }}</span>
        </li>
    @endforeach
</ul>
