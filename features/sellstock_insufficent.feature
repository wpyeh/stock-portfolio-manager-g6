Feature:Unsuccessful selling insufficient quantity
	When the user logs in and inputs a valid stock to sell but has too little quantity should output error message
Scenario:Insufficient Sell Stock
	Given user navigates to http://localhost/Frontend/login.html
	And fills Username as "halfond@usc.edu"
	And fills Password as "HelloWorld1!"
	And logs in
	And fills Ticker as "GOOG"
	And fills Quantity as "1000"
	And fills Name as "Google"
	And sells insufficient quantity stock
