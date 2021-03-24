<?php

class NI_Faker
{
    public static function generate(array $input)
    {
        $model = $input[0];
        $loop = $input[1] ?? 1;
        $static = $input[2] ?? null;
        $column = ORM::for_table($model)->raw_query("DESCRIBE $model")->findArray();
        $faker = Faker\Factory::create();
        for ($i=0; $i < $loop ; $i++) {
            $dataToCreate = [];

            foreach ($column as $item) {
                if ($item['Field'] == 'id') {
                    break;
                }
                if (isset($static) && array_key_exists($item['Field'], $static)) {
                    $value = $static[$item['Field']];
                    $dataToCreate[$item['Field']] = $value;
                    continue;
                }
                $type = explode('(', $item['Type'])[0];
                switch ($type) {
                    case 'int':
                        $value = $faker->randomDigit;
                        break;
                    case 'varchar':
                        $value = $faker->name;
                            if (strstr($item['Field'], 'phone')) {
                                $value = $faker->phoneNumber;
                            }
                            if (strstr($item['Field'], 'email')) {
                                $value = $faker->email;
                            }
                            if (strstr($item['Field'], 'user')) {
                                $value = $faker->userName;
                            }
                            if (strstr($item['Field'], 'pass')) {
                                $value = $faker->password;
                            }
                            if (strstr($item['Field'], 'url')) {
                                $value = $faker->url;
                            }
                            break;
                    case 'date':
                        $value = $faker->date;
                        break;
                    case 'double':
                        $value = $faker->latitude;
                        break;
                    case 'decimal':
                        $value = $faker->latitude;
                        break;
                    case 'float':
                        $value = $faker->latitude;
                        break;
                    case 'text':
                        $value = $faker->realText;
                        break;
                    default:
                        $value = $faker->name;
                        break;
                }
                $dataToCreate[$item['Field']] = $value;
            }
            $modelcall = 'model\\'.$model;
            $modelcall::create($dataToCreate);
        }
    }
}
