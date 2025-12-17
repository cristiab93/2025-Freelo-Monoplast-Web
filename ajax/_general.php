<?php

include_once "../_general.php";

function formatDateToSpanish($fecha_original) {
    $months_english = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $months_spanish = ["ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC"];

    $formatted_date = date("j F Y", strtotime($fecha_original));
    foreach ($months_english as $index => $month) {
        $formatted_date = str_replace($month, $months_spanish[$index], $formatted_date);
    }

    return strtoupper($formatted_date);
}

function formatDateToSpanishNoYears($fecha_original) {
    $months_english = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $months_spanish = ["ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC"];

    $formatted_date = date("j F", strtotime($fecha_original));
    foreach ($months_english as $index => $month) {
        $formatted_date = str_replace($month, $months_spanish[$index], $formatted_date);
    }

    return strtoupper($formatted_date);
}

function formatTime($time) {
    $dateTime = new DateTime($time);
    return $dateTime->format('G:i');
}


function differenceInHours($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Calcula la diferencia entre las fechas
    $interval = $start->diff($end);

    // Convierte el total de días en horas y añade las horas restantes
    $hours = ($interval->days * 24) + $interval->h;

    // Añade los minutos como fracción de horas
    $fractional_hours = $interval->i / 60;

    // Calcula el total de horas con fracciones
    $total_hours = $hours + $fractional_hours;

    // Añade las horas negativas si la fecha de inicio es posterior a la fecha final
    if ($interval->invert) {
        $total_hours = -$total_hours;
    }

    return $total_hours;
}

function diferenciaEnDias($fecha_inicio, $fecha_fin) {
    $date1 = new DateTime($fecha_inicio);
    $date2 = new DateTime($fecha_fin);
    $diff = $date1->diff($date2);

    // Si la fecha de inicio es mayor que la fecha de fin, devolver negativo
    $dias = $diff->days;
    if ($date2 > $date1) {
        $dias = -$dias;
    }

    return $dias;
}

function GetPOSTValues($values)
{
    $result = array();
    foreach($values as $value)
    {
        if (isset($_POST[$value])) {
            $result[$value] = $_POST[$value];
        } else {
            $result[$value] = null;
        }
    }
    return $result;
}

