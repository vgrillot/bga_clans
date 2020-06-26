QUERY
==
```
$sql = "SELECT COUNT(DISTINCT color_id) AS colors " .
       "FROM hut " .
       "WHERE territory_id = $territory_id ";
```


```
$qry = self::getObjectListFromDB($sql);
=> array(1) { [0]=> array(1) { ["colors"]=> string(1) "3" } } 
```

```
$qry = self::getCollectionFromDb($sql);
=> array(1) { [3]=> array(1) { ["colors"]=> string(1) "3" } } 
```

```
$qry = self::getUniqueValueFromDB($sql);
=> string(1) "3"
```
