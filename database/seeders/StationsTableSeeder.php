<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stations = [
            [ 'name' => 'Bratislava', 'longitude' => 17.1065192, 'latitude' => 48.1590802, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Trnava', 'longitude' => 17.5851162, 'latitude' => 48.3697054, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Šenkvice', 'longitude' => 17.3458085, 'latitude' => 48.3018213, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Cífer', 'longitude' => 17.4865681, 'latitude' => 48.3228939, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Báhoň', 'longitude' => 17.450056, 'latitude' => 48.3054287, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Bratislava Vinohrady', 'longitude' => 17.133183, 'latitude' => 48.1868375, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Bratislava Rača', 'longitude' => 17.1609356, 'latitude' => 48.2089149, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Svätý Jur', 'longitude' => 17.2178123, 'latitude' => 48.2476, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Pezinok', 'longitude' => 17.2707566, 'latitude' => 48.2825464, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Pezinok zast.', 'longitude' => 17.2461261, 'latitude' => 48.2670408, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Trenčín', 'longitude' => 18.0517708, 'latitude' => 48.8966106, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Žilina', 'longitude' => 18.7468722, 'latitude' => 49.2271775, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Liptovský Mikuláš', 'longitude' => 19.6057879, 'latitude' => 49.0920643, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Poprad-Tatry', 'longitude' => 20.294874, 'latitude' => 49.0599614, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Spišská Nová Ves', 'longitude' => 20.5614723, 'latitude' => 48.9503267, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Kysak', 'longitude' => 21.2237861, 'latitude' => 48.852878, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Košice', 'longitude' => 21.2690393, 'latitude' => 48.7222885, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Leopoldov', 'longitude' => 17.7587259, 'latitude' => 48.4416643, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Piešťany', 'longitude' => 17.8149138, 'latitude' => 48.5962492, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Trenčianska Teplá', 'longitude' => 18.1136396, 'latitude' => 48.9375919, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Dubnica nad Váhom', 'longitude' => 18.1677967, 'latitude' => 48.9662603, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Ilava', 'longitude' => 18.236353, 'latitude' => 49.002046, 'created_at' => now(), 'updated_at' => now()],
            [ 'name' => 'Púchov', 'longitude' => 18.3273953, 'latitude' => 49.1133965, 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert stations into the database
        DB::table('stations')->insert($stations);

        $this->command->info("Stations table seeded successfully.");
    }
}
