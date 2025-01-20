<?php

namespace App\Services\Image;

class DarkImageTemplate extends BaseImageTemplate
{
    protected function renderCityName(): void
    {
        $this->placeText($this->weatherData['city']['title_uk'] ?? $this->weatherData['city']['title_en'], 31, 0.12);
    }

    protected function renderDate(): void
    {
        $date = strtoupper($this->texts[strtolower(date('l'))] . ' ' . date('H:i', strtotime('+2 hours', strtotime($this->weatherData['current']['dt']))));

        $this->placeText($date, 40, 0.18);
    }

    protected function renderWeatherDescription(): void
    {
        $this->placeText($this->weatherData['current']['weather'][0]['description'], 40, 0.75);
    }

    protected function renderTemperature(): void
    {
        $text = strtoupper($this->weatherData['current']['temp'] . '°C');
        $this->placeText($text, 13, 0.7);
    }

    protected function renderWeatherIcon(): void
    {
        $this->renderIcon($this->weatherData['current']['icon_url'], 0.28, 2, 2.4);
    }

    protected function renderSunriseSunset(): void
    {
        $iconCoordinates = $this->renderIcon(__DIR__ . '/resources/templates/sunrise.png', 0.1, 5, 1.2);

        $sunrise = strtotime($this->weatherData['current']['sunrise']);
        $sunset = strtotime($this->weatherData['current']['sunset']);

        $labelText = time() > $sunrise ? $this->texts['sunrise_text'] : $this->texts['sunset_text'];
        $labelY = $this->getY($labelText, $iconCoordinates);

        $timeText = date('H:i', strtotime('+2 hours', (time() > $sunrise ? $sunset : $sunrise)));
        $timeFontSize = (int) ($this->width / 30);
        $timeFontFile = $this->getDefaultFont();
        $timeBox = imagettfbbox($timeFontSize, 0, $timeFontFile, $timeText);
        $timeTextWidth = $timeBox[2] - $timeBox[0];

        $timeX = $iconCoordinates['dstX'] + ($iconCoordinates['resW'] / 2) - ($timeTextWidth / 2);
        $timeY = $labelY + $timeFontSize + 10;

        $this->placeText($timeText, 30, null, $timeX, $timeY);
    }

    protected function renderWindDetails(): void
    {
        $iconCoordinates = $this->renderIcon(__DIR__ . '/resources/templates/wind.png', 0.1, 2, 1.2);

        $windValueText = $this->weatherData['current']['wind_speed'] . ' ' . $this->texts['ms'];
        $valueFontSize = (int) ($this->width / 30);
        $valueFontFile = $this->getDefaultFont();
        $valueBox = imagettfbbox($valueFontSize, 0, $valueFontFile, $windValueText);
        $valueTextWidth = $valueBox[2] - $valueBox[0];

        $valueX = $iconCoordinates['dstX'] + ($iconCoordinates['resW'] / 2) - ($valueTextWidth / 2);
        $valueY = $this->getY($this->texts['wind_text'], $iconCoordinates) + $valueFontSize + 10;

        $this->placeText($windValueText, 30, null, $valueX, $valueY);
    }

    protected function renderCloudDetails(): void
    {
        $iconCoordinates = $this->renderIcon(__DIR__ . '/resources/templates/clouds.png', 0.1, 1.26, 1.2);

        $cloudsText = $this->texts['clouds_text'];
        $cloudsFontSize = (int) ($this->width / 50);
        $cloudsFontFile = $this->getDefaultFont();

        $cloudsTextBox = imagettfbbox($cloudsFontSize, 0, $cloudsFontFile, $cloudsText);
        $cloudsTextWidth = $cloudsTextBox[2] - $cloudsTextBox[0];

        $cloudsTextX = $iconCoordinates['dstX'] + ($iconCoordinates['resW'] / 2) - ($cloudsTextWidth / 2);
        $cloudsTextY = $iconCoordinates['dstY'] + $iconCoordinates['resH'] + 15;

        $this->placeText($cloudsText, 50, null, $cloudsTextX, $cloudsTextY);

        $percentText = $this->weatherData['current']['clouds'] . '%';
        $percentFontSize = (int) ($this->width / 30);
        $percentFontFile = $this->getDefaultFont();

        $percentTextBox = imagettfbbox($percentFontSize, 0, $percentFontFile, $percentText);
        $percentTextWidth = $percentTextBox[2] - $percentTextBox[0];

        $percentTextX = $iconCoordinates['dstX'] + ($iconCoordinates['resW'] / 2) - ($percentTextWidth / 2);
        $percentTextY = $cloudsTextY + $percentFontSize + 10;

        $this->placeText($percentText, 30, null, $percentTextX, $percentTextY);
    }

    protected function applyBorder(): void
    {
        imagesetstyle($this->image, [imagecolorallocate($this->image, 255, 255, 255)]);
        imagearc(
            $this->image,
            (int) ($this->width / 2),
            (int) ($this->height / 1.27),
            30, 1, 0, 360,
            IMG_COLOR_STYLED,
        );
    }

    protected function getY(string $labelText, array $iconCoordinates): mixed
    {
        $labelFontSize = (int) ($this->width / 50);
        $labelFontFile = $this->getDefaultFont();
        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontFile, $labelText);
        $labelTextWidth = $labelBox[2] - $labelBox[0];

        $labelX = $iconCoordinates['dstX'] + ($iconCoordinates['resW'] / 2) - ($labelTextWidth / 2);
        $labelY = $iconCoordinates['dstY'] + $iconCoordinates['resH'] + 10;

        $this->placeText($labelText, 50, null, $labelX, $labelY);
        return $labelY;
    }

    protected function applyBackground(): void
    {

    }
}
