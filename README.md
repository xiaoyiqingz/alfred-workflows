#### Common

1. script file 使用 main-script.sh with /bin/bash ，因为workflow包需要php > 7.4.0, 如果本地php版本 > 7.4.0,  也可以直接使用/usr/bin/php

```
$query = urlencode( "{query}" );
require_once("jen.php");
```

2. script file 之后使用open url

3. 需设置env: url, name, token(token或密码)

#### Obsidian Search

需要安装 `Advanced URI` 与 `Local REST API` 插件
