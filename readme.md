# Project: Issues (Ticket Support System)

## Description

"Issues" is a simple ticket support system which allows employers of organizations to create and submit reports
concerning the organization. These reports can be issues and problems that employers have and they want a way to 
inform the department which is responsible, to handle and resolve them.
The department which is responsible to handle these reports, can update their values and run filters to
display only the desired ones for them. With this system users can have a centralized way to keep all their work documented
and managed so they can work more efficiently.

More specificaly users can:
	- Submit issues
	- Login as administrators
	- As Admis they also can:
		- See all the issues
		- Filter issues by state, priority and date
		- Create reports (.csv) with the given filters
		- Alter most values of the issues

## Install and run localy in a testing environment

	- Download and install WAMP: 3.2.6
		- MySQL: 5.7.36
		- PHP: 7.0.33 ... 7.0.33
		
	- Clone the project in C://wamp/www/

	- Execute WAMP from C://wamp/wampmanager.exe

	- Open http://localhost/phpmyadmin and login
		- username: "root"
		- password: -
		- server choice: MySQL
		
	- Create a new database with name 'issuesdb' (Encoding: utf8mb4)
	
	- Go to SQL Query tab in phpmyadmin and paste the query from C://wamp/www/issuesdb.sql

	- Open http://localhost/issues to see the submit form
	
	- Go to http://localhost/issues/login.php to see the administrator panel.
		- Login to administrator panel with:
		- username: "admin"
		- password: "]znae.zvMPdyIgUX"

