<?php

namespace App\Services\Image;

class WeeklyImageTemplate extends BaseImageTemplate
{
    public function renderCityName(): void
    {
        $this->placeText($this->weatherData['city']['title_uk'] ?? $this->weatherData['city']['title_en'], 25, 0.13);
    }

    public function applyBackground(): void
    {
        $configImages = $this->weatherProperties[$this->weatherData['current']['weather'][0]['id']]['img'];

        $this->renderIcon($configImages[array_rand($configImages)], 0, 0, 0, true);

        $color = imagecolorallocatealpha($this->image, 0, 0, 0, 50);
        imagefilledrectangle($this->image, 0, 0, 600, 800, $color);
    }

    protected function renderWeatherDescription(): void
    {
        $this->placeText($this->weatherData['current']['weather'][0]['description'], 25, 0.18);
    }

    protected function renderTemperature(): void
    {
        $this->placeText($this->weatherData['current']['temp'] . '°', 10, 0.28);

        $positionY = ($this->height * 0.37);
        foreach ($this->weatherData['daily'] as $dailyData) {
            $positionY += 75;
            $this->placeText(
                $dailyData['temp']['day'] . '°',
                30,
                null,
                ($this->width / 2.1),
                $positionY
            );
        }
    }

    protected function renderWeatherIcon(): void
    {
        $this->renderIcon($this->weatherData['current']['icon_url'], 0.15, 2, 2.9);

        $positionY = (int)($this->height * 0.30);

        foreach (array_slice($this->weatherData['daily'], 1, 6) as $dailyData) {
            $iconToPaste = imagecreatefrompng($dailyData['icon_url']);
            $iconWidth = imagesx($iconToPaste);
            $iconHeight = imagesy($iconToPaste);

            $need = (int)($this->width * 0.15);
            $koef = $need / $iconWidth;
            $resultW = $iconWidth * $koef;
            $resultH = $iconHeight * $koef;

            $x2 = $resultW;
            $y2 = $resultH;

            $dstX = (int)(($this->width / 1.5));
            $positionY += 74;

            imagecopyresampled(
                $this->image, $iconToPaste,
                $dstX, $positionY,
                0, 0,
                $x2, $y2,
                $iconWidth, $iconHeight
            );
        }
    }

    protected function applyBorder(): void
    {
        $positionY = (int)($this->height * 0.37);

        foreach (array_slice($this->weatherData['daily'], 1, 6) as $dailyData) {
            $positionY += 75;

            $text = strtoupper($this->texts[strtolower(date('l', strtotime($dailyData['dt'])))]);

            $this->placeText($text, 25, null, 10, $positionY);
        }
    }

    public function getDefaultFont(): string
    {
        return __DIR__ . '/resources/fonts/Montserrat-Light.otf';
    }

    protected function renderSunriseSunset(): void
    {
    }

    protected function renderWindDetails(): void
    {
    }

    protected function renderCloudDetails(): void
    {
    }

    protected function renderDate(): void
    {
    }
}
