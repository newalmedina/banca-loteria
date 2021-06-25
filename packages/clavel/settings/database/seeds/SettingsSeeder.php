<?php

use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = $this->findSetting('google_analytics_client_id');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Google Analytics Client ID',
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
            ])->save();
        }

        $setting = $this->findSetting('google_recaptcha_id');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Google reCAPTCHA ID',
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
            ])->save();
        }
    }

    /**
     * [setting description].
     *
     * @param [type] $key [description]
     *
     * @return [type] [description]
     */
    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}
