<?php

class TimeZoneList
{
    private $timezone_identifiers;
    private $error;

    public function __construct()
    {
        $this->error = '';
        $needver = '5.2.0';
        if (version_compare(PHP_VERSION, $needver, '<=')) {
            $this->error = 'Класс TimeZoneList требует PHP версии не ниже ' . $needver . '. Ваша версия: ' . PHP_VERSION;
            return;
        }
        $this->timezone_identifiers = DateTimeZone::listIdentifiers();
    }

    public function get_array()
    {
        $timezones = array();
        if (!empty($this->error)) return array(0 => $this->error);
        $phpTime = Date("Y-m-d H:i:s");
        foreach ($this->timezone_identifiers as $key => $value) {
            if (preg_match('/^(Europe|America|Asia|Antartica|Arctic|Atlantic|Indian|Pacific)\//', $value)) {
                $timezone = new DateTimeZone ($value);
                $offset = $timezone->getOffset(new DateTime($phpTime));
                $offsetHours = abs($offset) / 3600;
                $offsetString = ($offset < 0 ? '-' : '+');
                if (abs($offsetHours) == 1)
                    $label = 'час';
                else if (abs($offsetHours) > 1 and abs($offsetHours) < 5)
                    $label = 'часа';
                else
                    $label = 'часов';
                $timezones[$value] = array(
                    'timezone' => $value . ' (' . $offsetString . $offsetHours . ' ' . $label . ')',
                    'offset' => $offset
                );
            }
        }

        uasort($timezones, function ($a, $b) {
            return $a['offset'] != $b['offset'] ? $b['offset'] < $a['offset'] : $b['timezone'] < $a['timezone'];
        });

        return $timezones;
    }

    public function get_html_list($name = 'timeZoneList', $selected = 'Europe/Moscow')
    {
        $timezones = $this->get_array();
        $content = '';
        foreach ($timezones as $key => $value) {
            $content .= '<option value="' . $key . '" id="tz_' . $key . '"' . ($key == $selected ? ' selected' : '') .
                '>' . $value['timezone'] . "\n";
        }
        return $content;
    }
}
