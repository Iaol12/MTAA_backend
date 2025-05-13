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
            [ 'name' => 'Bratislava hl.st.', 'longitude' => 17.1065192, 'latitude' => 48.1590802 ],
            [ 'name' => 'Trnava', 'longitude' => 17.5851162, 'latitude' => 48.3697054 ],
            [ 'name' => 'Šenkvice', 'longitude' => 17.3458085, 'latitude' => 48.3018213 ],
            [ 'name' => 'Cífer', 'longitude' => 17.4865681, 'latitude' => 48.3228939 ],
            [ 'name' => 'Báhoň', 'longitude' => 17.450056, 'latitude' => 48.3054287 ],
            [ 'name' => 'Bratislava Vinohrady', 'longitude' => 17.133183, 'latitude' => 48.1868375 ],
            [ 'name' => 'Bratislava Rača', 'longitude' => 17.1609356, 'latitude' => 48.2089149 ],
            [ 'name' => 'Svätý Jur', 'longitude' => 17.2178123, 'latitude' => 48.2476 ],
            [ 'name' => 'Pezinok', 'longitude' => 17.2707566, 'latitude' => 48.2825464 ],
            [ 'name' => 'Pezinok zast.', 'longitude' => 17.2461261, 'latitude' => 48.2670408 ],
            [ 'name' => 'Trenčín', 'longitude' => 18.0517708, 'latitude' => 48.8966106 ],
            [ 'name' => 'Žilina', 'longitude' => 18.7468722, 'latitude' => 49.2271775 ],
            [ 'name' => 'Liptovský Mikuláš', 'longitude' => 19.6057879, 'latitude' => 49.0920643 ],
            [ 'name' => 'Poprad-Tatry', 'longitude' => 20.294874, 'latitude' => 49.0599614 ],
            [ 'name' => 'Spišská Nová Ves', 'longitude' => 20.5614723, 'latitude' => 48.9503267 ],
            [ 'name' => 'Kysak', 'longitude' => 21.2237861, 'latitude' => 48.852878 ],
            [ 'name' => 'Košice', 'longitude' => 21.2690393, 'latitude' => 48.7222885 ],
            [ 'name' => 'Leopoldov', 'longitude' => 17.7587259, 'latitude' => 48.4416643 ],
            [ 'name' => 'Piešťany', 'longitude' => 17.8149138, 'latitude' => 48.5962492 ],
            [ 'name' => 'Trenčianska Teplá', 'longitude' => 18.1136396, 'latitude' => 48.9375919 ],
            [ 'name' => 'Dubnica nad Váhom', 'longitude' => 18.1677967, 'latitude' => 48.9662603 ],
            [ 'name' => 'Ilava', 'longitude' => 18.236353, 'latitude' => 49.002046 ],
            [ 'name' => 'Púchov', 'longitude' => 18.3273953, 'latitude' => 49.1133965 ],
            [ 'name' => 'Považská Bystrica', 'longitude' => 18.432255, 'latitude' => 49.122510 ],
            [ 'name' => 'Vrútky', 'longitude' => 18.923854, 'latitude' => 49.115421],
            [ 'name' => 'Kraľovany', 'longitude' => 19.131775, 'latitude' => 49.153113],
            [ 'name' => 'Ružomberok', 'longitude' => 19.308419, 'latitude' => 49.084017],
            [ 'name' => 'Štrba', 'longitude' => 20.066729, 'latitude' => 49.082944],
            [ 'name' => 'Margecany', 'longitude' => 21.014208, 'latitude' => 48.894476],
        
            [ 'name' => 'Bratislava-Vajnory', 'longitude' => 17.20759, 'latitude' => 48.20563 ],
            [ 'name' => 'Ivanka pri Dunaji', 'longitude' => 17.2554, 'latitude' => 48.18675 ],
            [ 'name' => 'Bernolákovo', 'longitude' => 17.2731, 'latitude' => 48.2195 ],
            [ 'name' => 'Senec', 'longitude' => 17.40043, 'latitude' => 48.21951 ],
            [ 'name' => 'Reca', 'longitude' => 17.4667, 'latitude' => 48.2333 ],
            [ 'name' => 'Pusté Úľany', 'longitude' => 17.5715, 'latitude' => 48.2192 ],
            [ 'name' => 'Sládkovičovo', 'longitude' => 17.63852, 'latitude' => 48.20137 ],
            [ 'name' => 'Galanta', 'longitude' => 17.7269, 'latitude' => 48.1911 ],
        ];
         




        // Insert stations into the database
        DB::table('stations')->insert($stations);

        $this->command->info("Stations table seeded successfully.");
    }
}
