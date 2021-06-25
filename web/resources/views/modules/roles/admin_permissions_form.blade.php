<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />


{!! Form::open(array('role' => 'form','id'=>'frm_Permission_Role', 'method'=>'post')) !!}
{!! Form::hidden('id', $id, array('id' => 'id')) !!}
{!! Form::hidden('results', '', array('id' => 'results')) !!}

<div class="row">

    <div class="col-lg-12">
        <p>{{ trans('roles/lang.informacion_permisos') }}</p>

        {!! "<ol id='checkboxes'>" !!}
        <?php $actDepth = 0; ?>

        @foreach($permissionsTree as $key=>$value)

        @if($actDepth!=$value->depth)
        @if($actDepth>$value->depth)
        @for($nX=$actDepth;$nX>$value->depth; $nX--)
        </ol>
        </li>
        @endfor
        @endif
        <?php $actDepth=$value->depth; ?>
        @endif

        @if($value->depth==0)
            {!! "<li class='expanded'>" !!}
        @else
            {!! "<li>" !!}
        @endif

        @if($value->isRoot())
            <input type='checkbox' id="root" value='root' />
            <i class="fa fa-folder text-yellow" style="font-size: 18px; margin-left: 5px; margin-right: 5px;"></i>
            {{ trans('roles/lang.todos') }}
            @if($value->descendants()->count()>0)
                {!! "<ol>" !!}
            @else
                {!! "</li>" !!}
            @endif
        @else
            @if($value->descendants()->count()>0)
                <input type='checkbox' value="{{ $value->permission["id"] }}" @if(in_array($value->permission["id"], $a_arrayPermisos)) checked @endif />
                <i class="fa fa-folder text-yellow" style="font-size: 18px; margin-left: 5px; margin-right: 5px;"></i>
                {{ $value->permission["display_name"] }}
                {!! "<ol>" !!}
            @else
                <input type='checkbox' value='{{ $value->permission["id"] }}' @if(in_array($value->permission["id"], $a_arrayPermisos)) checked @endif />
                <i class="fa fa-key text-green" style="font-size: 15px; margin-left: 5px; margin-right: 5px;"></i>
                {{ $value->permission["display_name"] }}
                {!! "</li>" !!}
            @endif
        @endif

        @endforeach

        @if($actDepth>0)
            @for($nX=$actDepth;$nX>0; $nX--)
                {!! "</ol>" !!}
                {!! "</li>" !!}
            @endfor
        @endif

        {!! "</ol>" !!}

    </div>
</div>

{!! Form::close() !!}

<br clear="all">

<div class="box-footer">

    <a href="{{ url('/admin/roles') }}" class="btn btn-default">{{ trans('roles/lang.cancelar') }}</a>
    @if(Auth::user()->can('admin-roles-update'))
        <button onclick="sendInfo();" class="btn btn-info pull-right">{{ trans('roles/lang.guardar') }}</button>
    @endif

</div>


<script type="text/javascript" src="{{ asset('/assets/admin/vendor/jquery-bonsai/js/jquery.bonsai.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/admin/vendor/jquery-qubit/js/jquery.qubit.js')}}"></script>

<script>
    $(document).ready(function() {

        $('#checkboxes').bonsai({
            expandAll: false,
            checkboxes: true
        });

    });

    function sendInfo() {
        var sendUrlId = "";

        $("#frm_Permission_Role").attr("action", "{!! url("admin/roles/permissions/update") !!}");


        $("#checkboxes input").each(function() {
            if(($(this).val()!='' && $(this).attr("id")!='root') && ($(this).is(":checked") || $(this).is(":indeterminate"))) {
                if(sendUrlId!='') sendUrlId+=",";
                sendUrlId+=$(this).val();
            }
        });

        if(sendUrlId!='') {
            $("#results").val(sendUrlId);
            $("#frm_Permission_Role").submit();
        } else {
            $("#modal_alert").addClass('modal-warning');
            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> {!! trans('roles/lang.seleccione_un_permiso') !!}");
            $("#modal_alert").modal('toggle');
        }

    }
</script>
