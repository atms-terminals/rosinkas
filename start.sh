# устанавливаем считку
sudo modprobe ftdi_sio
sudo echo 0403 1234 > /sys/bus/usb-serial/drivers/ftdi_sio/new_id
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0
sudo chown www-data:www-data /dev/ttyUSB0

# запускаем купюрник и принтер
cd /home/trem/terminal/dist
../jdk/jre/bin/java -jar dispatcher.jar  dispatcher.properties log4j.properties
                                                                                  