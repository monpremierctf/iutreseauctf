log=user11
pass=password1
code=YOLO
url=https://localhost/yoloctf/register.php

curl -v --insecure -d "login=$log&password=$pass&code=$code" -X POST $url
