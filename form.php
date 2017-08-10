<form action="http://127.0.0.1/test/get.php" method="GET">
  <input type="submit" value="Submit">
</form>
<?php
$array = array("1" => "1","2" => "2");
$return = uCurl("http://127.0.0.1/test/get.php","PUT",$array);
print_r($return);


function uCurl( $url,$method,$params=array(),$header=''){  
        $curl = curl_init();//初始化CURL句柄  
        $timeout = 15;  
        curl_setopt($curl, CURLOPT_URL, $url);//设置请求的URL  
        curl_setopt($curl, CURLOPT_HEADER, false);// 不要http header 加快效率  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出  
  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);  
  
       
  
        curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);//设置连接等待时间  
        switch ($method){  
            case "GET" :  
                curl_setopt($curl, CURLOPT_HTTPGET, true);break;  
            case "POST":  
                curl_setopt($curl, CURLOPT_POST,true);  
                curl_setopt($curl, CURLOPT_NOBODY, true);  
                curl_setopt($curl, CURLOPT_POSTFIELDS,$params);break;//设置提交的信息  
            case "PUT" :  
                curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "PUT");  
  
                curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($params));break;  
            case "DELETE":  
                curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "DELETE");  
                curl_setopt($curl, CURLOPT_POSTFIELDS,$params);break;  
        }  
  
        $data = curl_exec($curl);//执行预定义的CURL  
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);//获取http返回值  
        curl_close($curl);  
        $res = json_decode($data,true);//var_dump($res);  
        return ['status'=>$status,'result'=>$res];  

    }  
?>