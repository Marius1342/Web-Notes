# Task Manager Web

*The docs for the API*

__Written in php__


## Calls

Every Response is in Json format

Url | Params   | Response
-------- | ---------- | ----------
id=? |The id of your project  | Every id of your notes
id=?&nd=?   |id=Project nd=node id      | The node with the full html

Examples:

*URL: ?id=0* 

```Response
{
"Status":"202",
"Length":"2",
"id":[
"0",
"1"
]
}
```

*URL: ?id=0&nd=1*


```Response
{
"Status":"202",
"tr":"THE FULL HTML"
}
```


Code | Expiation
-------- | --------
202 | Okay
404 | Project/Node not found
403 | Forbidden you have to activate the refresh in db.php

