{!! Form::open(array('role' => 'form','id'=>'frm_Role_User', 'method'=>'post')) !!}
{!! Form::hidden('id', $user->id, array('id' => 'id')) !!}
{!! Form::hidden('results', '', array('id' => 'results')) !!}

<?php $nPrinter=0; ?>
@foreach($roles as $key=>$value)
    @if($nPrinter==0 || $nPrinter%4==0)
        @if($nPrinter!=0) </div> @endif
        <div class="row">
    @endif

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div id="sel_{{ $value->id }}" class="box @if($user->hasRole($value->name)) box-success @else box-default @endif box-solid" style="cursor: pointer;" onclick="javascript:selected('sel_{{ $value->id }}');" data-value="{{ $value->id }}">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $value->display_name }}</h3>
            </div>
            <div class="box-body">
                <span class="info-box-icon" style="background: none; height: auto; line-height: 64px;"><i class="fa fa-user-plus"></i></span>
                {{ $value->description }}
            </div>
        </div>
    </div>

    <?php $nPrinter++; ?>

    @endforeach
    @if($nPrinter!=0) </div> @endif

{!! Form::close() !!}


<br clear="all">

<div class="box-footer">

    <a href="{{ url('/admin/users') }}" class="btn btn-default">{{ trans('users/lang.cancelar') }}</a>
    @if(Auth::user()->can('admin-users-update'))
        <button onclick="sendInfo();" class="btn btn-info pull-right">{{ trans('users/lang.guardar') }}</button>
    @endif

</div>

<script>
    function selected(itemSel) {
        obj = $("#" + itemSel);

        if(obj.hasClass("box-success")) {
            obj.removeClass("box-success");
            obj.addClass("box-default");
        } else {
            obj.removeClass("box-default");
            obj.addClass("box-success");
        }
    }

    function sendInfo() {
        var sendUrlId = "";

        $("#frm_Role_User").attr("action", "{!! url("admin/users/roles/update") !!}");

        $("#frm_Role_User .box-success").each(function() {
            if(sendUrlId!='') sendUrlId+=",";
            sendUrlId+=$(this).attr("data-value");
        });

        if(sendUrlId!='') {
            $("#results").val(sendUrlId);
            $("#frm_Role_User").submit();
        } else {
            $("#modal_alert").addClass('modal-warning');
            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> {!! trans('users/lang.seleccione_un_rol') !!}");
            $("#modal_alert").modal('toggle');
        }

    }
</script>
