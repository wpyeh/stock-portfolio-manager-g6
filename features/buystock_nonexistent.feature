Feature:Unsuccessful buying non existent stock
	When the user logs in and inputs a invalid stock to buy should output error message
Scenario:Nonexistent Buy Stock
	Given user navigates to http://localhost/Frontend/login.html
	And fills Username as "halfond@usc.edu"
	And fills Password as "HelloWorld1!"
	And logs in
	And fills Ticker as "GOOXEDA"
	And fills Quantity as "1000"
	And fills Name as "Google"
	And buys nonexistent stock
