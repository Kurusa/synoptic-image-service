<?php

require_once 'src/BaseImageTemplate.php';
require_once 'src/DarkImageTemplate.php';
require_once 'src/WeeklyImageTemplate.php';

use App\Services\Image\DarkImageTemplate;
use App\Services\Image\WeeklyImageTemplate;

$templateType = $_POST['template'] ?? 'weekly';

$templateClass = match ($templateType) {
    'weekly' => WeeklyImageTemplate::class,
    default => DarkImageTemplate::class,
};

$imageMaker = new $templateClass();
$imageMaker->initializeImage();
$imageMaker->setTexts(require_once 'src/resources/texts.php');
$imageMaker->setWeatherProperties(require_once 'src/resources/weather_properties.php');
$imageMaker->setWeatherData($_POST['weatherData'] ?? json_decode(file_get_contents('weather_example.json'), true));
$filePath = $imageMaker->generateWeatherImage();

$fp = fopen($filePath, 'rb');

header('Content-Type: image/png');
header('Content-Length: ' . filesize($filePath));

fpassthru($fp);

unlink($filePath);
