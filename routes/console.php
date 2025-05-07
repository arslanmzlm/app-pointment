<?php

Schedule::command('app:appointment:reminder')
    ->dailyAt('12:00');
