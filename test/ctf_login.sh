log=user2
pass=password2
code=YOLO
url=https://localhost/yoloctf/login.php

curl -v --insecure -d "login=$log&password=$pass" -X POST $url