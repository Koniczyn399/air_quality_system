<?php

namespace App\Helpers;

class PollutionColorHelper
{
    public static function getPollutionColor($value, $parameterName)
    {
        switch ($parameterName) {
            case 'PM1':
                if ($value <= 10) {
                    return '#00e400'; 
                } elseif ($value <= 20) {
                    return '#ffff00'; 
                } elseif ($value <= 30) {
                    return '#ff7e00'; 
                } else {
                    return '#ff0000'; 
                }

            case 'PM2.5':
                if ($value <= 12) {
                    return '#00e400';
                } elseif ($value <= 35.4) {
                    return '#ffff00';
                } elseif ($value <= 55.4) {
                    return '#ff7e00';
                } else {
                    return '#ff0000';
                }

            case 'PM10':
                if ($value <= 54) {
                    return '#00e400';
                } elseif ($value <= 154) {
                    return '#ffff00';
                } elseif ($value <= 254) {
                    return '#ff7e00';
                } else {
                    return '#ff0000';
                }

            case 'Wilgotność':
                if ($value <= 30) {
                    return '#ff7e00';
                } elseif ($value <= 60) {
                    return '#00e400';
                } else {
                    return '#ffff00';
                }

            case 'Ciśnienie':
                if ($value <= 1000) {
                    return '#ff7e00'; 
                } elseif ($value <= 1020) {
                    return '#00e400'; 
                } else {
                    return '#ffff00';
                }

            case 'Temperatura':
                if ($value <= 18) {
                    return '#0000ff';
                } elseif ($value <= 24) {
                    return '#00e400';
                } else {
                    return '#ff0000';
                }

            default:
                return '#808080';
        }
    }
}