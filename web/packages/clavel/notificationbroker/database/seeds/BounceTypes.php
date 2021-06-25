<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BounceTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bouncetypes')->delete();

        $menuType = new Clavel\NotificationBroker\Models\BounceType();
        $menuType->id = 1;
        $menuType->active = true;
        $menuType->name = "unknown";
        $menuType->description = "Desconocido";
        $menuType->save();

        $menuType = new Clavel\NotificationBroker\Models\BounceType();
        $menuType->id = 2;
        $menuType->active = true;
        $menuType->name = "hard";
        $menuType->description = "Rebote duro o hard bounce: la razón de la «no entrega» de tu email es permanente. ".
            "La dirección del destinatario no existe. Esto ocurre cuando alguien se equivoca al ponerte el email o simplemente lo hace adrede porque no quiere dejar su email de verdad y se lo inventa. ".
            "El nombre de dominio no existe. Igual razón que la anterior.".
            "El ISP de tu destinatario por alguna razón desconocida ha bloqueado completamente la entrega. Debe ser una razón desconocida ya que existen razones por las que entrarían a ser rebotes blandos.";
        $menuType->save();


        $menuType = new Clavel\NotificationBroker\Models\BounceType();
        $menuType->id = 3;
        $menuType->active = true;
        $menuType->name = "soft";
        $menuType->description = "Rebote blando o soft bounce: la razón de la «no entrega» de tu email es temporal. ".
            "Los rebotes suaves suelen indicar un problema de entregabilidad temporal. Un problema que ocurre en este momento pero que antes no pasaba y que se espera que se solucione. ".
            "Existe ese email y que es de alguien pero que por alguna razón algo está fallando. Tu ISP lo entiende y bueno, no le da mucha importancia y se mantienen atento a este asunto.".
            "El buzón de tu cliente está lleno. Ha superado la cuota de espacio que tienen todas las cuentas incluso las de Gmail, lo que pasa que en este caso es una cuota muy alta y raramente se da. Pero se puede dar en alguna ocasión.".
            "El servidor de correo electrónico se ha caído o está fuera de línea. Puede pasar con los ISP privados, con los grandes como Google es difícil que esto ocurra.".
            "El mensaje de correo electrónico es demasiado pesado.".
            "La cuenta de correo electrónico existe pero está inactiva por alguna razón.";
        $menuType->save();
    }
}
