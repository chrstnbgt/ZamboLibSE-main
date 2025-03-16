<?php

function formatDate($dateString) {
    $date = new DateTime($dateString);
    $today = new DateTime('today');
    $yesterday = new DateTime('yesterday');

    if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
        return 'Today ' . $date->format('h:i A');
    } elseif ($date->format('Y-m-d') === $yesterday->format('Y-m-d')) {
        return 'Yesterday ' . $date->format('h:i A');
    } else {
        return $date->format('M j, Y');
    }
}

function formatTime($timeString) {
    return date('h:i a', strtotime($timeString));
}

function formatDay($dateString) {
    $date = date('Y-m-d', strtotime($dateString));
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    if ($date == $today) {
        return 'Today';
    } elseif ($date == $yesterday) {
        return 'Yesterday';
    } else {
        return date('M j, Y', strtotime($dateString));
    }
}
