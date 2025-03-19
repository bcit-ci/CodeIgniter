<?xml version="1.0" encoding="UTF-8"?>
<package packagerversion="1.8.0" version="2.0"
	xmlns="http://pear.php.net/dtd/package-2.0" xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://pear.php.net/dtd/tasks-1.0
				http://pear.php.net/dtd/tasks-1.0.xsd
				http://pear.php.net/dtd/package-2.0
				http://pear.php.net/dtd/package-2.0.xsd">
	<name>Requests</name>
	<channel>pear.ryanmccue.info</channel>
	<summary>A HTTP library written in PHP, for human beings.</summary>
	<description>
			Requests is a HTTP library written in PHP, for human beings. It is
			roughly based on the API from the excellent Requests Python library.
			Requests is ISC Licensed (similar to the new BSD license) and has
			no dependencies.
	</description>
	<lead>
		<name>Ryan McCue</name>
		<user>rmccue</user>
		<email>me+pear@ryanmccue dot info</email>
		<active>yes</active>
	</lead>
	<date>{{ date }}</date>
	<time>{{ time }}</time>
	<version>
		<release>{{ version }}</release>
		<api>{{ api_version }}</api>
	</version>
	<stability>
		<release>{{ stability }}</release>
		<api>{{ stability }}</api>
	</stability>
	<license uri="https://github.com/rmccue/Requests/blob/master/LICENSE" filesource="LICENSE">ISC</license>
	<notes>-</notes>
	<contents>
		<dir name="/">
			<file name="CHANGELOG.md" role="doc" />
			<file name="LICENSE" role="doc" />
			<file name="README.md" role="doc" />
			<dir name="library">
				<file install-as="Requests.php" name="Requests.php" role="php" />
				<dir name="Requests">
{{ files }}
				</dir>
			</dir>
			<file name="library/Requests/Transport/cacert.pem" install-as="library/Requests/Transport/cacert.pem" role="data" />
		</dir>
	</contents>
	<dependencies>
		<required>
			<php>
				<min>5.2.0</min>
			</php>
			<pearinstaller>
				<min>1.4.0</min>
			</pearinstaller>
		</required>
	</dependencies>
	<phprelease />
</package>