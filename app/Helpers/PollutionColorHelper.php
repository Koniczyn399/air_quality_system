<?php

namespace App\Helpers;

class PollutionColorHelper
{
    public static function getPollutionColor($value, $parameterName = null, $parameterTag = null)
    {
        $v = is_numeric($value) ? floatval($value) : 0.0;
        $raw = $parameterTag ?: ($parameterName ?: '');
        $norm = strtolower(str_replace(['_', '-', '.', ' '], '', $raw));

        if ($norm === 'pm1') {
            if ($v <= 10) return '#2ecc71';
            if ($v <= 20) return '#f39c12';
            if ($v <= 30) return '#e67e22';
            return '#c0392b';
        }

        if ($norm === 'pm25' || $norm === 'pm2.5') {
            if ($v <= 12) return '#2ecc71';
            if ($v <= 35.4) return '#f39c12';
            if ($v <= 55.4) return '#e67e22';
            return '#c0392b';
        }

        if ($norm === 'pm10') {
            if ($v <= 54) return '#2ecc71';
            if ($v <= 154) return '#f39c12';
            if ($v <= 254) return '#e67e22';
            return '#c0392b';
        }

        if ($norm === 'hum' || $norm === 'wilgotnosc') {
            if ($v <= 30) return '#e67e22';
            if ($v <= 60) return '#2ecc71';
            return '#f39c12';
        }

        if ($norm === 'press' || $norm === 'pressure' || $norm === 'cisnienie') {
            if ($v < 1000) return '#e67e22';
            if ($v <= 1020) return '#2ecc71';
            return '#f39c12';
        }

        if ($norm === 'temp' || $norm === 'temperatura' || $norm === 'temperature') {
            if ($v <= 0) return '#3498db';
            if ($v <= 18) return '#16a085';
            if ($v <= 24) return '#f39c12';
            return '#e67e22';
        }

        return '#808080';
    }
}