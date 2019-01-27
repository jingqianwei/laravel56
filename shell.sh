#!/bin/bash
step=1 #间隔的秒数

for (( i = 0; i < 60; i=(i+step) )); do
    /usr/local/php/bin/php /home/chinwe/work/laravel56/artisan schedule:run
    sleep $step
done

exit 0
