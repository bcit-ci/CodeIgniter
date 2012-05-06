#!/bin/bash

case "${1:-''}" in
        'start')
                if test -f /tmp/selenium.pid
                then
                        echo "Selenium is already running."
                else
                        java -jar /usr/lib/selenium/selenium-server-standalone-2.21.0.jar > /var/log/selenium/selenium-output.log 2> /var/log/selenium/selenium-error.log & echo $! > /tmp/selenium.pid
                        echo "Starting Selenium..."

                        error=$?
                        if test $error -gt 0
                        then
                                echo "${bon}Error $error! Couldn't start Selenium!${boff}"
                        fi
                fi
        ;;
        'stop')
                if test -f /tmp/selenium.pid
                then
                        echo "Stopping Selenium..."
                        PID=`cat /tmp/selenium.pid`
                        kill -3 $PID
                        if kill -9 $PID ;
                                then
                                        sleep 2
                                        test -f /tmp/selenium.pid && rm -f /tmp/selenium.pid
                                else
                                        echo "Selenium could not be stopped..."
                                fi
                else
                        echo "Selenium is not running."
                fi
                ;;
        'restart')
                if test -f /tmp/selenium.pid
                then
                        kill -HUP `cat /tmp/selenium.pid`
                        test -f /tmp/selenium.pid && rm -f /tmp/selenium.pid
                        sleep 1
                        java -jar /usr/lib/selenium/selenium-server-standalone-2.21.0.jar > /var/log/selenium/selenium-output.log 2> /var/log/selenium/selenium-error.log & echo $! > /tmp/selenium.pid
                        echo "Reload Selenium..."
                else
                        echo "Selenium isn't running..."
                fi
                ;;
        *)      # no parameter specified
                echo "Usage: $SELF start|stop|restart"
                exit 1
        ;;
esac
