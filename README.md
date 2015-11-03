#Speedycloud-storage sdk

>####support composer：
composer require "simcu/speedycloud-storage" "1.0"

####1. how to use
```
use Speedycloud\Storage\Actor;
```
####2. init action
```
$scs = new Actor;
$scs->init('access_key','secret_key','bucket');
```

####3. Get all files in bucket (return array)
```    
$scs->getObjects()
```
####4. create new object (return boolean)
```
$scs->newObject($remote, $content)
```

####5. set object acl (return boolean)
```    
$acl = 'public-read'
$scs->aclObject($name,$acl)
```

####6. delete the object (return boolean)
```    
$scs->delObject($name)
```

####7. get the object (return string of the content)
```    
$scs->getObject($name)
```

####8. used with laravel
```     
use Speedycloud\Storage\FlysystemAdapter;
```

```
Storage::extend('speedycloud',function($app,$config){     
    $client = new FlysystemAdapter($config['accessKey'],$config['secretKey'],$config['bucket']);     
    return new Filesystem($client); 
});
```
