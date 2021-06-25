<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsBrokerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_broker_type', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_fixed')->default(0);
            $table->boolean('is_modifiable')->default(0);
            $table->string('slug')->unique();
            $table->timestamps();
        });


        Schema::create('notifications_broker_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_type_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();

            $table->unique(['notification_type_id','locale'], 'notification_type_unique');
            $table->foreign('notification_type_id', "not_type_trans_id_foreign")->references('id')
                ->on('notifications_broker_type')
                ->onDelete('cascade');
        });

        Schema::create('notifications_broker_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string("archivo", 255)->nullable()->unique();
            $table->string("titulo", 255)->nullable();
            $table->string("tipo", 50)->nullable()->index();
            $table->string("slug", 255)->nullable()->index();
            $table->string("subject", 255)->nullable();
            $table->longText("mensaje")->nullable();
            $table->boolean('is_generated')->nullable();

            $table->timestamps();
        });

        Schema::create('notifications_broker_group', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->default(null);

            $table->string('fichero_group')->default('');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        // Estado de projectos
        Schema::create('notifications_broker_status', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 10);
            $table->string('slug', 20)->index();
            $table->timestamps();
        });
        $notifications_broker_status = [
            array(
                'id' => 0,
                'color' => '#FFD23F',
                'slug' => 'pending',
                'name' => 'Pendiente de envio',
                'description' => 'Pendiente de envio'
            ),
            array(
                'id' => 1,
                'color' => '#73D67F',
                'slug' => 'sent',
                'name' => 'Enviado correcto',
                'description' => 'Enviado correcto'
            ),
            array(
                'id' => 2,
                'color' => '#E63B2E',
                'slug' => 'error',
                'name' => 'Error de envio',
                'description' => 'Error de envio'

            ),
            array(
                'id' => 4,
                'color' => '#FF7733',
                'slug' => 'retrying',
                'name' => 'Reintentando envío',
                'description' => 'Reintentando envío'

            ),
            array(
                'id' => 5,
                'color' => '#1F487E',
                'slug' => 'delayed',
                'name' => 'Envido retrasado',
                'description' => 'Envido retrasado'

            )
        ];



        for ($i=0; $i<sizeof($notifications_broker_status); $i++) {
            $state = array(
                'id' => $notifications_broker_status[$i]['id'],
                'color' =>  $notifications_broker_status[$i]['color'],
                'slug' =>  $notifications_broker_status[$i]['slug'],
                'name' => $notifications_broker_status[$i]['name'],
                'description' => $notifications_broker_status[$i]['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            DB::table('notifications_broker_status')->insert($state);
        }


        // Estado de projectos
        Schema::create('notifications_broker_certificate_status', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 10);
            $table->string('slug', 20)->index();
            $table->timestamps();
        });
        $notifications_broker_certificate_status = array(
            array(
                'id' => 0,
                'color' => '#FFD23F',
                'slug' => 'pending',
                'name' => 'Pendiente de envio',
                'description' => 'Pendiente de envio'
            ),
            array(
                'id' => 1,
                'color' => '#73D67F',
                'slug' => 'sent',
                'name' => 'Enviado correcto',
                'description' => 'Enviado correcto'
            ),
            array(
                'id' => 2,
                'color' => '#E63B2E',
                'slug' => 'error',
                'name' => 'Error de envio',
                'description' => 'Error de envio'

            ),
            array(
                'id' => 4,
                'color' => '#FF7733',
                'slug' => 'delivered',
                'name' => 'Entregado en buzón',
                'description' => 'Entregado en buzón'

            ),
            array(
                'id' => 5,
                'color' => '#1F487E',
                'slug' => 'read',
                'name' => 'Leido',
                'description' => 'Leido'

            )
        );
        for ($i=0; $i<sizeof($notifications_broker_certificate_status); $i++) {
            $state = array(
                'id' => $notifications_broker_certificate_status[$i]['id'],
                'color' =>  $notifications_broker_certificate_status[$i]['color'],
                'slug' =>  $notifications_broker_certificate_status[$i]['slug'],
                'name' => $notifications_broker_certificate_status[$i]['name'],
                'description' => $notifications_broker_certificate_status[$i]['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            DB::table('notifications_broker_certificate_status')->insert($state);
        }

        Schema::create('notifications_broker_blacklist', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 10);
            $table->string('to')->unique();
            $table->timestamps();
        });

        Schema::create('bouncetypes', function (Blueprint $table) {
            $table->increments('id');

            $table->boolean('active')->default(0);
            $table->string('name');
            $table->text('description')->nullable();


            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('bouncedemails', function (Blueprint $table) {
            $table->increments('id');

            $table->boolean('active')->default(0);
            $table->string('email');
            $table->text('description')->nullable();
            $table->string('bounce_code');
            $table->unsignedInteger('bounce_type_id')->nullable();
            $table->foreign('bounce_type_id', 'bounce_type_id_fk_966600')->references('id')->on('bouncetypes');

            $table->timestamps();
        });



        Schema::create('notifications_broker', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug_type')->index();
            $table->text('payload');
            $table->text('message');
            $table->string('guid', 100)->default('')->index();
            $table->unsignedInteger('user_id');
            $table->integer('response_code')->nullable();
            $table->text('response_info');
            $table->datetime('sent_at')->nullable();
            $table->unsignedInteger('retries')->default(0);
            $table->datetime('retry_at')->nullable();
            $table->unsignedInteger('notification_group_id')->nullable()->default(null);
            $table->string('status_slug', 20)->default('pending')->index();
            $table->decimal('credits', 5, 2)->default(1);
            $table->datetime('validated_at')->nullable()->index();
            $table->boolean('is_certified')->default(0)->index();
            $table->string('platform_uid')->nullable()->index();
            $table->string('origin_uid')->nullable()->index();
            $table->string('certificate_file')->nullable();
            $table->string('certificate_status_slug', 20)->nullable();
            $table->string('broker', 30)->default('')->index();
            $table->string('receiver')->default('')->index();


            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('notification_group_id')
                ->references('id')->on('notifications_broker_group')
                ->onDelete('cascade');
        });

        Schema::create('notifications_broker_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('sms_credits', 8, 2)->default(0);
            $table->decimal('sms_credits_limit', 8, 2)->default(0);
            $table->datetime('sms_verified_at')->nullable();
            $table->datetime('sms_limit_notified_at')->nullable();
            $table->string('sms_limit_notify_to')->default('');
            $table->timestamps();
        });

        $config = array(
            'sms_credits' =>  0,
            'sms_credits_limit' =>  5000,
            'sms_verified_at' => Carbon::now(),
            'sms_limit_notify_to' => 'info@aduxia.com; mgallardo@aduxia.com',
            'sms_limit_notified_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

        DB::table('notifications_broker_settings')->insert($config);

        Schema::create('notifications_broker_entity', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('slug')->default('');
            $table->string('sender_name')->default('');
            $table->string('logo_path')->default('');
            $table->string('logo_width')->default('');
            $table->string('logo_height')->default('');
            $table->string('address')->default('');
            $table->string('color', 10)->default('');
            $table->timestamps();
        });

        $config = array(
            'name' =>  'Broker',
            'slug' =>  'broker',
            'sender_name' => 'Broker Notificaciones',
            'logo_path' => '/assets/front/img/logo.png',
            'logo_width' => '220',
            'logo_height' => '70',
            'address' => 'Notifications made with ♥',
            'color' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

        DB::table('notifications_broker_entity')->insert($config);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications_broker_entity');
        Schema::dropIfExists('notifications_broker_settings');

        Schema::table('notifications_broker', function (Blueprint $table) {
            $table->dropForeign('notifications_broker_notification_group_id_foreign');
            $table->dropForeign('notifications_broker_user_id_foreign');
        });
        Schema::dropIfExists('notifications_broker');

        Schema::dropIfExists('bouncedemails');
        Schema::dropIfExists('bouncetypes');
        Schema::dropIfExists('notifications_broker_blacklist');

        Schema::table('notifications_broker_group', function (Blueprint $table) {
            $table->dropForeign('notifications_broker_group_user_id_foreign');
        });
        Schema::dropIfExists('notifications_broker_group');

        Schema::dropIfExists('notifications_broker_certificate_status');
        Schema::dropIfExists('notifications_broker_status');
        Schema::dropIfExists('notifications_broker_templates');
        Schema::dropIfExists('notifications_broker_type_translations');
        Schema::dropIfExists('notifications_broker_type');
    }
}
