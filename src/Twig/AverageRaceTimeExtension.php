<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AverageRaceTimeExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('averageTime', [$this, 'AverageTime']),
        ];
    }

    public function AverageTime($value, $distance)
    {
        $times = array();
        foreach ($value as $item) {

            if ($item->getDistance() == $distance) {
                $times[] = $item->getRaceTime();
            }
        }

        if (count($times) != 0) {

        $averageSeconds = array_sum($times) / count($times);

        $hours = floor($averageSeconds / 3600);
        $mins = floor($averageSeconds / 60 % 60);
        $secs = floor($averageSeconds % 60);

        $averageTime = $hours . ':' . $mins . ':' . $secs;

        return $averageTime;
        }
    }
}
