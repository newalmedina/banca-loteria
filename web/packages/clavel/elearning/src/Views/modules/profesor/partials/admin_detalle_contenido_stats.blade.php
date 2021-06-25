<div id="information_stats">
    @foreach($a_tracking as $value)
        <div class="col-lg-3 col-md-6 col-xs-12 col-sm-6">
            <div class="info-box {{ $value["bg"] }}">
                <span class="info-box-icon"><i class="fa {{ $value["fa"] }}" aria-hidden="true"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $value["name"] }}</span>
                    <span class="info-box-number">{{ $value["total"] }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $value["porcentaje"] }}%"></div>
                    </div>
                    <span class="progress-description">
                      {!!  sprintf(trans("elearning::profesor/admin_lang.en_estado_actual"),$value["porcentaje"]) !!}
                    </span>
                </div>
            </div>
        </div>
    @endforeach

    <div class="col-lg-3 col-md-6 col-xs-12 col-sm-6">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-certificate" hidden="true" aria-hidden="true"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans("elearning::profesor/admin_lang.nota_media") }}</span>
                <span class="info-box-number">{{ $stats["nota_media"] }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ is_numeric($stats["nota_media"]) ? $stats["nota_media"]*10 : 0}}%"></div>
                </div>
                <span class="progress-description">
                {!!  sprintf(trans("elearning::profesor/admin_lang.nota_media_actual"),$stats["superan_media"]) !!}
                </span>
            </div>
        </div>
    </div>
</div>
