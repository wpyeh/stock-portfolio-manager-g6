Feature:Unsuccessful buying stock feature
	When the user logs in and inputs a valid stock they cannot buy with their balance should output error message
Scenario:Insufficent Funds Buy Stock
	Given user navigates to http://localhost/Frontend/login.html
	And fills Username as "halfond@usc.edu"
	And fills Password as "HelloWorld1!"
	And logs in
	And fills Ticker as "GOOG"
	And fills Quantity as "1000"
	And fills Name as "Google"
	And buys stock insufficient funds
