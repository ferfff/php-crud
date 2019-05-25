# CRUD REST

CRUD created for PHP without framework

import sql file in your DB

## GET INFO
Request method GET

// Get all the info in db
```bash
url/
``` 

// Get info by id
```bash
url?id=1
```

// search
```
url?firstname=a&surnames=ney&phone=55&email=r@r.com
```
// Search by one or two values
```
url?firstname=a&phone=55
```

## CREATE INFO
Request method POST

Json example:
```javascript
{
	"firstname": "Wayne",
	"surnames": "Rooney",
	"phone" : {
		"home" : "555555414",
		"cellphone" : "5554566155",
    	"work" : "5554566153",
    	"other" : "5554566111"
	},
	"email" : ["rooney@mufc.com", "wayne@mufc.com"]
}
```

## UPDATE INFO
Request method PUT

Json example:
```javascript
{
	"id": 1,
	"firstname": "Wayne A.",
	"surnames": "Rooney",
	"phone" : {
		"home" : "5555111113"
	},
	"email" : ["wr@mufc.com"]
}
```

## DELETE INFO
Request method DELETE

Json example:
```javascript
{
	"id": 1
}
```
