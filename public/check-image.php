<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$applicant = \App\Models\Applicant::first();

echo "<h2>Image Debug Info</h2>";
echo "<strong>Database Value:</strong> " . ($applicant->picture ?? 'NULL') . "<br>";
echo "<strong>Filename:</strong> " . basename($applicant->picture ?? '') . "<br><br>";

$filename = basename($applicant->picture ?? '');

// Check public folder
$publicPath = __DIR__ . '/applicant-pictures/' . $filename;
echo "<strong>Public Path:</strong> {$publicPath}<br>";
echo "<strong>Public Exists:</strong> " . (file_exists($publicPath) ? '✅ YES' : '❌ NO') . "<br><br>";

// Check storage folder
$storagePath = storage_path('app/public/' . $applicant->picture);
echo "<strong>Storage Path:</strong> {$storagePath}<br>";
echo "<strong>Storage Exists:</strong> " . (file_exists($storagePath) ? '✅ YES' : '❌ NO') . "<br><br>";

// Try URLs
echo "<h3>Try These URLs:</h3>";
echo "1. <a href='/applicant-pictures/{$filename}' target='_blank'>Public: /applicant-pictures/{$filename}</a><br>";
echo "2. <a href='/storage/applicant-pictures/{$filename}' target='_blank'>Storage: /storage/applicant-pictures/{$filename}</a><br>";
echo "3. <a href='/storage/{$applicant->picture}' target='_blank'>Full Path: /storage/{$applicant->picture}</a><br>";

// Show actual image if found
if (file_exists($publicPath)) {
    echo "<br><h3>Image Preview (from public folder):</h3>";
    echo "<img src='/applicant-pictures/{$filename}' style='width:200px;height:200px;object-fit:cover;border:2px solid #ccc;'>";
} elseif (file_exists($storagePath)) {
    echo "<br><h3>Image Preview (from storage folder):</h3>";
    echo "<img src='/storage/{$applicant->picture}' style='width:200px;height:200px;object-fit:cover;border:2px solid #ccc;'>";
}
// ```

// Then visit:
// ```
// http://127.0.0.1:8000/check-image.php
// ```

// This will show you:
// 1. ✅ Where the file actually exists
// 2. ✅ What's in the database
// 3. ✅ Which URL works
// 4. ✅ A preview of the image if found

// **Copy the output here** and I'll give you the exact code to fix it!

// ---

// ## Or Just Tell Me Manually

// Just check these two locations on your computer and tell me which one has the image file:

// **Location 1:**
// ```
// C:\xampp\htdocs\sample\public\applicant-pictures\01KHAWG85FMD0K7QY1672PW83V.jpg
// ```

// **Location 2:**
// ```
// C:\xampp\htdocs\sample\storage\app\public\applicant-pictures\01KHAWG85FMD0K7QY1672PW83V.jpg