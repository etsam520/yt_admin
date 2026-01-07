<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TradeNode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class __TradeNodesTableSeeder extends Seeder
{
    public function run()
    {
        // Clear existing nodes
        // TradeNode::truncate();

        // Create root nodes (continents)
        $continents = [
            'Europe',
            'Asia',
            'Africa',
            'North America',
            'South America',
            'Oceania',
            'Middle East'
        ];

        $continentNodes = [];
        foreach ($continents as $continent) {
            $continentNodes[] = TradeNode::create([
                'name' => $continent,
                'slug' => Str::slug($continent),
                'parent_id' => null
            ]);
        }

        // Create sub-regions for each continent
        $regions = [
            // Europe
            ['Western Europe', 'Northern Europe', 'Southern Europe', 'Eastern Europe'],
            // Asia
            ['East Asia', 'South Asia', 'Southeast Asia', 'Central Asia'],
            // Africa
            ['North Africa', 'West Africa', 'East Africa', 'Southern Africa', 'Central Africa'],
            // North America
            ['Eastern North America', 'Western North America', 'Caribbean', 'Central America'],
            // South America
            ['Northern South America', 'Southern South America', 'Andean Region'],
            // Oceania
            ['Australia', 'New Zealand', 'Pacific Islands', 'Melanesia'],
            // Middle East
            ['Arabian Peninsula', 'Levant', 'Mesopotamia']
        ];

        $regionNodes = [];
        foreach ($regions as $continentIndex => $regionList) {
            foreach ($regionList as $region) {
                $regionNodes[] = TradeNode::create([
                    'name' => $region,
                    'slug' => Str::slug($region),
                    'parent_id' => $continentNodes[$continentIndex]->id
                ]);
            }
        }

        // Create countries (about 150 total)
        $countries = [
            // Western Europe
            ['United Kingdom', 'France', 'Germany', 'Netherlands', 'Belgium', 'Luxembourg', 'Ireland'],
            // Northern Europe
            ['Sweden', 'Norway', 'Denmark', 'Finland', 'Iceland'],
            // Southern Europe
            ['Italy', 'Spain', 'Portugal', 'Greece', 'Malta', 'Cyprus'],
            // Eastern Europe
            ['Poland', 'Hungary', 'Czech Republic', 'Slovakia', 'Romania', 'Bulgaria'],

            // East Asia
            ['China', 'Japan', 'South Korea', 'Mongolia', 'Taiwan'],
            // South Asia
            ['India', 'Pakistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Bhutan'],
            // Southeast Asia
            ['Indonesia', 'Thailand', 'Vietnam', 'Malaysia', 'Philippines', 'Singapore', 'Myanmar'],
            // Central Asia
            ['Kazakhstan', 'Uzbekistan', 'Turkmenistan', 'Tajikistan', 'Kyrgyzstan'],

            // North Africa
            ['Morocco', 'Algeria', 'Tunisia', 'Libya', 'Egypt'],
            // West Africa
            ['Nigeria', 'Ghana', 'Ivory Coast', 'Senegal', 'Mali', 'Niger'],
            // East Africa
            ['Ethiopia', 'Kenya', 'Tanzania', 'Uganda', 'Rwanda', 'Somalia'],
            // Southern Africa
            ['South Africa', 'Namibia', 'Botswana', 'Zimbabwe', 'Mozambique'],
            // Central Africa
            ['DR Congo', 'Congo', 'Gabon', 'Cameroon', 'Chad'],

            // Eastern North America
            ['United States', 'Canada'],
            // Western North America
            ['Mexico', 'Western US', 'Western Canada'],
            // Caribbean
            ['Jamaica', 'Cuba', 'Bahamas', 'Dominican Republic', 'Puerto Rico'],
            // Central America
            ['Panama', 'Costa Rica', 'Nicaragua', 'Honduras', 'El Salvador', 'Guatemala'],

            // Northern South America
            ['Venezuela', 'Colombia', 'Guyana', 'Suriname', 'French Guiana'],
            // Southern South America
            ['Brazil', 'Argentina', 'Chile', 'Uruguay', 'Paraguay'],
            // Andean Region
            ['Peru', 'Bolivia', 'Ecuador'],

            // Australia
            ['Eastern Australia', 'Western Australia', 'Northern Territory'],
            // New Zealand
            ['North Island', 'South Island'],
            // Pacific Islands
            ['Fiji', 'Samoa', 'Tonga', 'Papua New Guinea'],
            // Melanesia
            ['Vanuatu', 'Solomon Islands', 'New Caledonia'],

            // Arabian Peninsula
            ['Saudi Arabia', 'Yemen', 'Oman', 'UAE', 'Qatar', 'Kuwait', 'Bahrain'],
            // Levant
            ['Syria', 'Lebanon', 'Jordan', 'Israel', 'Palestine'],
            // Mesopotamia
            ['Iraq', 'Kurdistan']
        ];

        $countryNodes = [];
        foreach ($countries as $regionIndex => $countryList) {
            foreach ($countryList as $country) {
                $countryNodes[] = TradeNode::create([
                    'name' => $country,
                    'slug' => Str::slug($country),
                    'parent_id' => $regionNodes[$regionIndex]->id
                ]);
            }
        }

        // Create major cities (about 50 more nodes to reach ~150)
        $majorCities = [
            // Europe
            ['London', 'Paris', 'Berlin', 'Madrid', 'Rome', 'Amsterdam', 'Brussels'],
            // Asia
            ['Tokyo', 'Shanghai', 'Mumbai', 'Delhi', 'Bangkok', 'Singapore', 'Hong Kong'],
            // Africa
            ['Cairo', 'Lagos', 'Nairobi', 'Johannesburg', 'Casablanca'],
            // North America
            ['New York', 'Los Angeles', 'Chicago', 'Toronto', 'Mexico City', 'Miami'],
            // South America
            ['Sao Paulo', 'Rio de Janeiro', 'Buenos Aires', 'Lima', 'Bogota'],
            // Middle East
            ['Dubai', 'Riyadh', 'Tel Aviv', 'Istanbul', 'Baghdad'],
            // Oceania
            ['Sydney', 'Melbourne', 'Auckland', 'Perth']
        ];

        foreach ($majorCities as $cityGroup) {
            foreach ($cityGroup as $city) {
                // Find a random country in the same region to attach to
                $randomCountry = $countryNodes[array_rand($countryNodes)];
                TradeNode::create([
                    'name' => $city,
                    'slug' => Str::slug($city),
                    'parent_id' => $randomCountry->id
                ]);
            }
        }

        // Output total count
        $totalNodes = TradeNode::count();
        $this->command->info("Created {$totalNodes} trade nodes.");
    }
}
