<?php

// Schedule runs from cron

Schedule::command('app:appointment:reminder')
    ->dailyAt('12:00');
