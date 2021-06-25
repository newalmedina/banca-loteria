<style>
    .has-error .text-control {
        border-color: #a94442;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    }

    .alert-container {
        margin-left: 40px;
        padding-right: 15px;
        padding-left: 15px;
    }

    .alert-question {
        padding: 15px;
        margin-bottom: 0;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-question h4 {
        margin-top: 0;
        color: inherit;
    }
    .alert-question .alert-question-link {
        font-weight: bold;
    }
    .alert-question > p,
    .alert-question > ul {
        margin-bottom: 0;
    }
    .alert-question > p + p {
        margin-top: 5px;
    }
    .alert-question-dismissable,
    .alert-question-dismissible {
        padding-right: 35px;
    }
    .alert-question-dismissable .close,
    .alert-question-dismissible .close {
        position: relative;
        top: -2px;
        right: -21px;
        color: inherit;
    }
    .alert-question-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }
    .alert-question-success hr {
        border-top-color: #c9e2b3;
    }
    .alert-question-success .alert-question-link {
        color: #2b542c;
    }
    .alert-question-info {
        background-color: #d9edf7;
        border-color: #bce8f1;
        color: #31708f;
    }
    .alert-question-info hr {
        border-top-color: #a6e1ec;
    }
    .alert-question-info .alert-question-link {
        color: #245269;
    }
    .alert-question-warning {
        background-color: #fcf8e3;
        border-color: #faebcc;
        color: #8a6d3b;
    }
    .alert-question-warning hr {
        border-top-color: #f7e1b5;
    }
    .alert-question-warning .alert-question-link {
        color: #66512c;
    }
    .alert-question-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }
    .alert-question-danger hr {
        border-top-color: #e4b9c0;
    }
    .alert-question-danger .alert-question-link {
        color: #843534;
    }

    /** SPINNER CREATION **/

    .loader {
        position: relative;
        text-align: center;
        margin: 15px auto 35px auto;
        z-index: 9999;
        display: block;
        width: 80px;
        height: 80px;
        border: 10px solid rgba(0, 0, 0, .3);
        border-radius: 50%;
        border-top-color: #000;
        animation: spin 1s ease-in-out infinite;
        -webkit-animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            -webkit-transform: rotate(360deg);
        }
    }

    @-webkit-keyframes spin {
        to {
            -webkit-transform: rotate(360deg);
        }
    }

    .loader-txt p {
        font-size: 13px;
        color: #666;
    }

    .loader-txt p small {
        font-size: 11.5px;
        color: #999;
    }

</style>
<script>
    function Reset() {
        var strBtn = "";
        $("#confirmModalLabel").html("{{ trans('elearning::contenidos/front_lang.repetir') }}");
        $("#confirmModalBody").html("{{ trans('elearning::contenidos/front_lang.resetear_eval') }}");
        strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/front_lang.cerrar') }}</button>';
        strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo();">{{ trans('general/front_lang.borrar_item') }}</button>';
        $("#confirmModalFooter").html(strBtn);
        $('#modal_confirm').modal('toggle');
    }

    function deleteinfo() {
        document.location = "{{ url("contenido/detalle-contenido/".$contenido->url_amigable."/".$contenido->id."/destroy") }}";
    }


    function saveEval() {
        //Tendremos que hacer un bucle de recorrer todas las preguntas y ver si están respondida.

        // Limpiamos los errores anteriores
        clearMessages();

        // Obtenemos todas las preguntas
        var questions = getDistinctQuestions();
        var listErrors = [];
        for (var i = 0; i < questions.length; i++) {
            // Para cada pregunta verificamos si estan respondidas
            verifyQuestion(questions[i], listErrors);
        }

        if(listErrors.length > 0) {
            // Mostramos errores
            setErrors(listErrors);

            // Vamos al primero de todos
            var elem = $('#'+listErrors[0]);
            elem.focus();

            // Mostramos el mensaje
            $("#modalFaltanRespuestas").modal("toggle");
        }else{
            var btnEnviar = $("#btnEnviarExamen");
            btnEnviar.addClass('disabled');
            btnEnviar.prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>&nbsp;');

            $("#sendingExamModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $("#formData").submit();
        }
    }

    function clearMessages() {
        var frm = $("#formData");

        frm.find('.has-success')
            .removeClass('has-success');
        frm.find('.has-error')
            .removeClass('has-error');
        $(".error").remove();


    }

    function getDistinctQuestions() {
        var els = Array.prototype.slice.call(document.querySelectorAll("[name^='answers']"));
        return els.reduce(function (result, el) {
            if (result.indexOf(el.id) === -1) {
                result.push(el.id);
            }
            return result;
        }, []);
    }

    function verifyQuestion(id, listErrors) {
        // Numero de respuestas rellenas
        var filled = 0;

        // Obtenemos todas las respuestas del mismo id
        var answers = document.querySelectorAll("[id='"+id+"']");
        for (var i = 0; i < answers.length; i++) {
            // Obtenemos la respuesta seleccionada
            var answer = answers[i];
            if(answer.type === 'textarea') {
                // Si es de tipo textarea vemos si hay algo dentro
                if(answer.value !== '') {
                    filled++;
                }
            } else {
                // Es de tipo radio o checkbox y vemos si esta marcada
                if(answer.checked) {
                    filled++;
                }
            }

            // Verificamos si está marcada como obligatoria y si no lo esta significa que puede estar vacia =>
            // la marcamos respondida
            if (!answer.classList.contains('obligatoria')) {
                filled++;
            }
        }

        // Si no hay nada es un error
        if(filled === 0) {
            listErrors.push(id);
        }
    }

    function setErrors(listErrors) {
        for (var j = 0; j < listErrors.length; j++) {
            errorPlacement(listErrors[j]);
        }
    }

    function errorPlacement(id) {
        // al padre div añadir has-error
        var elem = $('#'+id);

        elem.closest(".form-group").addClass('has-error');
        if (elem.attr('type') == 'radio' || elem.attr('type') == 'checkbox') {
            elem.closest(".radio-list").before('<em id="'+id+'-error" class="error help-block alert-container">' +
                '<div class="alert-question alert-question-danger">' +
                '{{ trans('elearning::contenidos/front_lang.responder_pregunta') }}' +
                '</div>' +
                '</em>');
        } else {
            elem.closest(".form-group").prepend('<em id="'+id+'-error" class="error help-block alert-container">' +
                '<div class="alert-question alert-question-danger">' +
                '{{ trans('elearning::contenidos/front_lang.responder_pregunta') }}' +
                '</div>' +
                '</em>');
        }

    }


</script>