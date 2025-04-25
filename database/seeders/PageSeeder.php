<?php
namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::create([
            'type' => 'About Us',
            'text' => '
                <h1>Welcome to Our Platform!</h1>
                <p>We are a team of professionals dedicated to providing the best service possible. Our platform is designed to <strong>streamline your experience</strong> and offer you the best features available. <em>Our mission is to bring innovation to our users.</em></p>
                <h2>What We Offer</h2>
                <ul>
                    <li>High-quality services</li>
                    <li>24/7 customer support</li>
                    <li>Flexible subscription plans</li>
                </ul>
                <p>We are committed to your satisfaction and are always working to improve our platform. <a href="contact-us">Contact us</a> to learn more about what we do.</p>
            ',
        ]);

        Page::create([
            'type' => 'Terms & Conditions',
            'text' => '
                <h1>Terms and Conditions</h1>
                <p>By using our platform, you agree to the following terms and conditions:</p>
                <h2>User Responsibilities</h2>
                <p>You are responsible for keeping your account information secure and following all our guidelines. Failure to comply may result in <strong>account suspension</strong>.</p>
                <h2>Privacy</h2>
                <p>We take your privacy seriously. Please read our <a href="privacy-policy">Privacy Policy</a> for more details.</p>
                <h2>Changes to Terms</h2>
                <p>We reserve the right to update these terms at any time. We will notify users of major changes via email.</p>
            ',
        ]);
    }
}
