Feature:Successful buying stock feature
	When the user logs in and inputs a valid stock they can buy with their balance should update their portfolio and balance.	
	
Scenario:Succesful Buy Stock
	Given user navigates to http://localhost/Frontend/login.html
	And fills Username as "halfond@usc.edu"
	And fills Password as "HelloWorld1!"
	And logs in
	And fills Ticker as "GOOG"
	And fills Quantity as "5"
	And fills Name as "Google"
	And buys stock successfully

