<?php

namespace App\Services\Image;

abstract class BaseImageTemplate
{
    protected int $width = 600;
    protected int $height = 800;
    protected $image;
    protected array $weatherData;
    protected array $texts;
    protected array $weatherProperties;

    public function setWeatherData(array $weatherData): void
    {
        $this->weatherData = $weatherData;
    }

    public function setTexts(array $texts): void
    {
        $this->texts = $texts;
    }

    public function setWeatherProperties(array $weatherProperties): void
    {
        $this->weatherProperties = $weatherProperties;
    }

    public function initializeImage($path = null): void
    {
        if ($path) {
            $this->image = imagecreatefrompng($path);
            $this->width = imagesx($this->image);
            $this->height = imagesy($this->image);
        } else {
            $this->image = imagecreatetruecolor($this->width, $this->height);
        }
    }

    public function generateWeatherImage(): string
    {
        $this->applyBackground();
        $this->renderCityName();
        $this->renderDate();
        $this->renderWeatherDescription();
        $this->renderTemperature();
        $this->renderWeatherIcon();
        $this->renderSunriseSunset();
        $this->renderWindDetails();
        $this->renderCloudDetails();
        $this->applyBorder();
        return $this->storeImage();
    }

    protected function applyBackground(): void
    {
        $bgColor = imagecolorallocate($this->image, 0, 0, 0);
        imagefill($this->image, 0, 0, $bgColor);

        $this->renderIcon($this->weatherData['current']['icon_url'], 0, 0, 0, true);

        $color = imagecolorallocatealpha($this->image, 0, 0, 0, 50);
        imagefilledrectangle($this->image, 0, 0, 600, 800, $color);
    }

    abstract protected function renderCityName(): void;

    abstract protected function renderDate(): void;

    abstract protected function renderWeatherDescription(): void;

    abstract protected function renderTemperature(): void;

    abstract protected function renderWeatherIcon(): void;

    abstract protected function renderSunriseSunset(): void;

    abstract protected function renderWindDetails(): void;

    abstract protected function renderCloudDetails(): void;

    abstract protected function applyBorder(): void;

    protected function storeImage(): string
    {
        $filename = 'image_' . uniqid() . '.png';

        $publicPath = __DIR__ . '/resources/backgrounds/' . $filename;

        imagepng($this->image, $publicPath);

        return $publicPath;
    }

    protected function placeText($text, $fontSize, $box = null, $x = null, $y = null, $fontFile = null): void
    {
        $fontColor = imagecolorallocate($this->image, 255, 255, 255);
        $fontSize = (int) ($this->width / $fontSize);
        $coo = $box ? $this->calculateBoxPosition($fontSize, $text, $box) : '';

        imagefttext(
            $this->image,
            $fontSize,
            0,
            (int) ($box ? $coo['x'] : $x),
            (int) ($box ? $coo['y'] : $y),
            $fontColor,
            $fontFile ?: $this->getDefaultFont(),
            $text
        );
    }

    protected function calculateBoxPosition(int $fontSize, $text, $posY, $fontFile = null): array
    {
        $box = imagettfbbox($fontSize, 0, $fontFile ?: $this->getDefaultFont(), $text);
        $positionX = (int) (($this->width / 2) - (($box[2] - $box[0]) / 2));
        $positionY = (int) ($this->height * $posY);

        return [
            'x' => $positionX,
            'y' => $positionY,
            'add' => $box,
        ];
    }

    protected function renderIcon(string $iconPath, $scaleFactor, $gridX, $gridY, $isFullScreen = false): array
    {
        $iconResource = imagecreatefrompng($iconPath);
        $iconOriginalWidth = imagesx($iconResource);
        $iconOriginalHeight = imagesy($iconResource);

        if ($isFullScreen) {
            $destinationX = $destinationY = 0;
            $iconScaledWidth = $this->width;
            $iconScaledHeight = $this->height;
        } else {
            $scaledWidth = (int) ($this->width * $scaleFactor);
            $scalingFactor = $scaledWidth / $iconOriginalWidth;
            $iconScaledWidth = $iconOriginalWidth * $scalingFactor;
            $iconScaledHeight = $iconOriginalHeight * $scalingFactor;

            $destinationX = (int) (($this->width / $gridX) - ($iconScaledWidth / 2));
            $destinationY = (int) (($this->height / $gridY) - ($iconScaledHeight / 2));
        }

        imagecopyresampled(
            $this->image, $iconResource,
            $destinationX, $destinationY,
            0, 0,
            $iconScaledWidth, $iconScaledHeight,
            $iconOriginalWidth, $iconOriginalHeight
        );

        return [
            'dstX' => $destinationX,
            'dstY' => $destinationY,
            'resW' => $iconScaledWidth,
            'resH' => $iconScaledHeight,
        ];
    }

    public function getDefaultFont(): string
    {
        return __DIR__ . '/resources/fonts/Montserrat-Regular.otf';
    }
}
