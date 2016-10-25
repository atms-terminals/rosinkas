cd release
find * -type f | grep .php | while read file; do
	echo $file
	php -w $file > $file.tmp;
	mv $file.tmp $file;
done