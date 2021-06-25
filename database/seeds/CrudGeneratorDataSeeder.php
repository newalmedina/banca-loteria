<?php


use Clavel\CrudGenerator\Models\FieldType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrudGeneratorDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crud_field_types')->delete();

        $fieldTypes = array(
            [1, 'Auto incremento', 'auto_increment', false],
            [2, 'Text', 'text', true],
            [3, 'Email', 'email', true],
            [4, 'Textarea', 'textarea', true],
            [5, 'Password', 'password', true],
            [6, 'Radio', 'radio', true],
            [19, 'Radio si/no', 'radio_yes_no', true],
            [7, 'Select', 'select', true],
            [8, 'Checkbox', 'checkbox', true],
            [9, 'Integer', 'number', true],
            [10, 'Float', 'float', true],
            [11, 'Money', 'money', true],
            [12, 'Date Picker', 'date', true],
            [13, 'Date / Time Picker', 'datetime', true],
            [14, 'Time Picker', 'time', true],
            [15, 'File', 'file', true],
            [17, 'BelongsTo Relationship', 'belongsToRelationship', true],
            [18, 'BelongsToMany Relationship', 'belongsToManyRelationship', true],
            [20, 'Color', 'color', true],
            [21, 'Checkbox Multiple', 'checkboxMulti', true],
            [22, 'Image', 'image', true]
        );

        foreach ($fieldTypes as $fieldType) {
            $type = new FieldType();
            $type->id = $fieldType[0];
            $type->name = $fieldType[1];
            $type->slug = $fieldType[2];
            $type->active = $fieldType[3];
            $type->save();
        }
    }
}
