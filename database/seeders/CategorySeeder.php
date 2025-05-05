<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // video_categories
        $video_categories = [
            'Welcome to NOBL',
            'Introduction',
            'Key to success in this industry',
            'Door approach / Pitch',
            'Transitioning',
            'Building Value',
            'Qualify Questions',
            'Buying Atmosphere',
            'Amply Value',
            'Drop Price / Compare Price',
            'Closing Lines',
            'Area Management',
            'How to use your IPad Resources',
        ];
        foreach ($video_categories as $vcategory) {
            Category::create([
                'type' => 'Video Category',
                'name' => $vcategory,
            ]);
        }
        // image_categories
        $image_categories = [
            'PayScale\'s',
            'PayScale\'s',
            'Slicks',
            'Career Progress Sheets',
            'Agreements Examples',
            'BASAFASA Information',
            'Blitz Trips',
            'Incentives',
            'Playbook',
        ];
        foreach ($image_categories as $icategory) {
            Category::create([
                'type' => 'Image Category',
                'name' => $icategory,
            ]);
        }
        // documents_categories
        $documents_categories = [
            'PayScale\'s',
            'PayScale\'s',
            'Slicks',
            'Career Progress Sheets',
            'Agreements Examples',
            'BASAFASA Information',
            'Blitz Trips',
            'Incentives',
            'Playbook',
        ];
        foreach ($documents_categories as $dcategory) {
            Category::create([
                'type' => 'Documents Category',
                'name' => $dcategory,
            ]);
        }
        // audio_categories
        $audio_categories = [
            'Welcome to NOBL',
            'Introduction',
            'Key to success in this industry',
            'Door approach / Pitch',
            'Transitioning',
            'Building Value',
            'Qualify Questions',
            'Buying Atmosphere',
            'Amply Value',
            'Drop Price / Compare Price',
            'Closing Lines',
            'Area Management',
            'How to use your IPad Resources',
        ];
        foreach ($audio_categories as $acategory) {
            Category::create([
                'type' => 'Audio Category',
                'name' => $acategory,
            ]);
        }
    }
}
