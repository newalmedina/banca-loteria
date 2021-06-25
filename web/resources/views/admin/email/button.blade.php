<style>


    .button{
        border-radius: 3px !important;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16) !important;
        display: inline-block !important;
        text-decoration: none !important;
        -webkit-text-size-adjust: none !important;
        color: #FFF !important;
        background-color: #c24d48;
        border-top: 10px solid #c24d48;
        border-right: 18px solid #c24d48;
        border-bottom: 10px solid #c24d48;
        border-left: 18px solid #c24d48;
    }
</style>

<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" aria-hidden="true">
    <tr>
        <td align="center">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" aria-hidden="true">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0" aria-hidden="true">
                            <tr>
                                <td>
                                    <a href="{!! @$payload['password'] !!}/{{ $token}}" class="button" target="_blank">{{ trans("auth/lang.info_04_front") }}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
